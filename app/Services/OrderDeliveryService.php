<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDelivery;
use App\Models\OrderDeliveryItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Partial delivery tracking. Order lines come from the immutable orders.product_json snapshot
 * and are identified by a positional line_key ("i" for a simple product, "i-j" for attribute j
 * of the variant product at index i). Delivered qty = sum of all delivery rounds.
 */
class OrderDeliveryService
{
    public const STATUS_PARTIAL = 'partially_delivered';
    public const STATUS_DELIVERED = 'delivered';

    /**
     * Flattened order lines with ordered / delivered / pending quantities.
     *
     * @return array<int, array{key: string, name: string, detail: ?string, ordered: int, delivered: int, pending: int}>
     */
    public function lines(Order $order): array
    {
        $delivered = OrderDeliveryItem::query()
            ->whereHas('delivery', fn ($q) => $q->where('order_id', $order->id))
            ->selectRaw('line_key, SUM(qty) as qty')
            ->groupBy('line_key')
            ->pluck('qty', 'line_key');

        $lines = [];
        foreach ($this->snapshot($order) as $i => $row) {
            $name = $row['product']['name'] ?? 'Item';
            $variantName = $row['variant']['name'] ?? null;
            $attributes = (! empty($row['attributes']) && is_array($row['attributes'])) ? $row['attributes'] : [];

            if (! $attributes) {
                // Same qty fallback as the admin order detail view.
                $lines[] = $this->line((string) $i, $name, null, (int) ($row['qty'] ?? 1), $delivered);

                continue;
            }

            foreach ($attributes as $j => $attr) {
                $option = trim(($attr['attribute_name'] ?? '').' '.(! empty($attr['option_name']) ? '- '.$attr['option_name'] : ''));
                $detail = implode(' · ', array_filter([$variantName, $option]));
                $lines[] = $this->line($i.'-'.$j, $name, $detail ?: null, (int) ($attr['qty'] ?? 1), $delivered);
            }
        }

        return $lines;
    }

    public function pendingQty(Order $order): int
    {
        return array_sum(array_column($this->lines($order), 'pending'));
    }

    public function hasDeliveries(Order $order): bool
    {
        return $order->deliveries()->exists();
    }

    /**
     * Record one delivery round and move the order to partially_delivered / delivered.
     *
     * @param  array<string, int|string|null>  $quantities  line_key => qty delivered in this round
     *
     * @throws ValidationException
     */
    public function record(Order $order, array $quantities, string $deliveredOn, ?string $note, ?User $by): OrderDelivery
    {
        return DB::transaction(function () use ($order, $quantities, $deliveredOn, $note, $by) {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);

            if (strtolower((string) $order->order_status) === 'cancelled') {
                throw ValidationException::withMessages(['qty' => 'A cancelled order cannot be delivered.']);
            }

            $lines = collect($this->lines($order))->keyBy('key');
            $rows = [];

            foreach ($quantities as $key => $qty) {
                $qty = (int) $qty;
                if ($qty === 0) {
                    continue;
                }

                $line = $lines->get((string) $key);
                if (! $line) {
                    throw ValidationException::withMessages(['qty' => 'Unknown order line.']);
                }
                if ($qty < 0 || $qty > $line['pending']) {
                    throw ValidationException::withMessages([
                        'qty' => "{$line['name']}: delivered qty must be between 0 and {$line['pending']} (pending).",
                    ]);
                }

                $rows[] = ['line_key' => (string) $key, 'qty' => $qty];
            }

            if (! $rows) {
                throw ValidationException::withMessages(['qty' => 'Enter a delivered quantity for at least one item.']);
            }

            $delivery = OrderDelivery::create([
                'order_id' => $order->id,
                'delivered_on' => $deliveredOn,
                'note' => $note,
                'created_by' => $by?->id,
            ]);
            $delivery->items()->createMany($rows);

            $this->syncStatus($order);

            return $delivery;
        });
    }

    /** Set order status from delivered quantities (only once something has been delivered). */
    public function syncStatus(Order $order): void
    {
        if (! $this->hasDeliveries($order)) {
            return;
        }

        if ($this->pendingQty($order) > 0) {
            $order->update(['order_status' => self::STATUS_PARTIAL]);

            return;
        }

        // Payment status is left alone: it follows verified payments (OrderPaymentService).
        $order->update(['order_status' => self::STATUS_DELIVERED]);
    }

    protected function snapshot(Order $order): array
    {
        $decoded = json_decode((string) $order->product_json, true);

        return is_array($decoded) ? array_values($decoded) : [];
    }

    protected function line(string $key, string $name, ?string $detail, int $ordered, $delivered): array
    {
        $done = min((int) ($delivered[$key] ?? 0), $ordered);

        return [
            'key' => $key,
            'name' => $name,
            'detail' => $detail,
            'ordered' => $ordered,
            'delivered' => $done,
            'pending' => $ordered - $done,
        ];
    }
}
