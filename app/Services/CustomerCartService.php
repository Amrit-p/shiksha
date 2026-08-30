<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Variant;
use App\Models\VariantAttribute;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CustomerCartService
{
    public const SESSION_KEY = 'customer_enquiry_cart';

    public function all(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public function count(): int
    {
        return collect($this->all())->sum('qty');
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function add(array $payload): array
    {
        $productId = (int) ($payload['product_id'] ?? 0);
        $variantId = ! empty($payload['variant_id']) ? (int) $payload['variant_id'] : null;
        $variantAttributeId = ! empty($payload['variant_attribute_id']) ? (int) $payload['variant_attribute_id'] : null;
        $qty = max(1, (int) ($payload['qty'] ?? 1));

        $product = Product::query()
            ->where('status', 1)
            ->find($productId);

        if (! $product) {
            return ['success' => false, 'message' => 'Product not found or unavailable.'];
        }

        $variant = null;
        $variantAttribute = null;
        $unitPrice = $product->sale_price ?: $product->price;
        $sku = null;
        $variationLabel = null;
        $image = $product->image;

        if ($variantId) {
            $variant = Variant::query()
                ->with('variantname')
                ->where('product_id', $product->id)
                ->where('status', 1)
                ->find($variantId);

            if (! $variant) {
                return ['success' => false, 'message' => 'Selected variation is unavailable.'];
            }

            $unitPrice = $variant->sell_price ?: $variant->price ?: $unitPrice;
            $sku = $variant->sku ?: $variant->catalog_number;
            $variationLabel = $variant->name ?: ($variant->variantname->name ?? null);
            $image = $variant->image ?: $image;
        }

        if ($variantAttributeId) {
            $variantAttribute = VariantAttribute::query()
                ->with(['option', 'attribute'])
                ->where('status', 1)
                ->find($variantAttributeId);

            if (! $variantAttribute || ($variant && $variantAttribute->variant_id !== $variant->id)) {
                return ['success' => false, 'message' => 'Selected attribute option is unavailable.'];
            }

            $unitPrice = $variantAttribute->sell_price ?: $variantAttribute->mrp ?: $unitPrice;
            $image = $variantAttribute->image ?: $image;
            $parts = array_filter([
                $variationLabel,
                optional($variantAttribute->attribute)->name,
                optional($variantAttribute->option)->name ?: $variantAttribute->attribute_value,
            ]);
            $variationLabel = implode(' / ', $parts);
        }

        $key = $this->itemKey($productId, $variantId, $variantAttributeId);
        $cart = $this->all();

        if (isset($cart[$key])) {
            $cart[$key]['qty'] += $qty;
        } else {
            $cart[$key] = [
                'key' => $key,
                'product_id' => $product->id,
                'variant_id' => $variantId,
                'variant_attribute_id' => $variantAttributeId,
                'title' => $product->title,
                'slug' => $product->slug,
                'sku' => $sku,
                'variation_label' => $variationLabel,
                'image' => $image,
                'unit_price' => $unitPrice !== null ? (float) $unitPrice : null,
                'qty' => $qty,
            ];
        }

        Session::put(self::SESSION_KEY, $cart);

        return [
            'success' => true,
            'message' => 'Added to enquiry cart.',
            'count' => collect($cart)->sum('qty'),
            'items' => array_values($cart),
        ];
    }

    public function updateQty(string $key, int $qty): array
    {
        $cart = $this->all();

        if (! isset($cart[$key])) {
            return ['success' => false, 'message' => 'Cart item not found.'];
        }

        if ($qty < 1) {
            unset($cart[$key]);
        } else {
            $cart[$key]['qty'] = $qty;
        }

        Session::put(self::SESSION_KEY, $cart);

        return [
            'success' => true,
            'message' => 'Cart updated.',
            'count' => collect($cart)->sum('qty'),
            'items' => array_values($cart),
        ];
    }

    public function remove(string $key): array
    {
        $cart = $this->all();
        unset($cart[$key]);
        Session::put(self::SESSION_KEY, $cart);

        return [
            'success' => true,
            'message' => 'Item removed.',
            'count' => collect($cart)->sum('qty'),
            'items' => array_values($cart),
        ];
    }

    public function validatedItems(): Collection
    {
        $items = collect();

        foreach ($this->all() as $item) {
            $product = Product::query()->where('status', 1)->find($item['product_id'] ?? 0);
            if (! $product) {
                continue;
            }

            $items->push(array_merge($item, [
                'product' => $product,
            ]));
        }

        return $items;
    }

    protected function itemKey(int $productId, ?int $variantId, ?int $variantAttributeId): string
    {
        return implode(':', [
            $productId,
            $variantId ?: 0,
            $variantAttributeId ?: 0,
        ]);
    }
}
