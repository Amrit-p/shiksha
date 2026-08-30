<?php

namespace App\Services;

use App\Mail\TemplatedMail;
use App\Models\Enquiry;
use App\Models\EnquiryActivity;
use App\Models\EnquiryItem;
use App\Models\EnquiryStatus;
use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EnquiryService
{
    public function __construct(
        protected CustomerCartService $cart,
        protected MailTemplateService $mailTemplates,
        protected SmtpConfigurator $smtp
    ) {}

    public function createFromCart(array $customerData, ?string $ip = null, ?string $userAgent = null): Enquiry
    {
        $items = $this->cart->validatedItems();

        if ($items->isEmpty()) {
            throw new \RuntimeException('Your enquiry cart is empty or contains unavailable products.');
        }

        $pending = EnquiryStatus::query()->where('slug', 'pending')->first()
            ?? EnquiryStatus::query()->orderBy('sort_order')->first();

        if (! $pending) {
            throw new \RuntimeException('Enquiry statuses are not configured.');
        }

        $enquiry = DB::transaction(function () use ($customerData, $items, $pending, $ip, $userAgent) {
            $enquiry = Enquiry::create([
                'enquiry_number' => $this->generateNumber(),
                'name' => $customerData['name'],
                'email' => $customerData['email'],
                'phone' => $customerData['phone'],
                'company' => $customerData['company'] ?? null,
                'address' => $customerData['address'] ?? null,
                'city' => $customerData['city'] ?? null,
                'state' => $customerData['state'] ?? null,
                'pin_code' => $customerData['pin_code'] ?? null,
                'message' => $customerData['message'] ?? null,
                'enquiry_status_id' => $pending->id,
                'source' => 'website',
                'ip_address' => $ip,
                'user_agent' => Str::limit((string) $userAgent, 250),
            ]);

            foreach ($items as $item) {
                EnquiryItem::create([
                    'enquiry_id' => $enquiry->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'variant_attribute_id' => $item['variant_attribute_id'] ?? null,
                    'product_title' => $item['title'],
                    'product_sku' => $item['sku'] ?? null,
                    'variation_label' => $item['variation_label'] ?? null,
                    'product_image' => $item['image'] ?? null,
                    'unit_price' => $item['unit_price'] ?? null,
                    'qty' => (int) $item['qty'],
                ]);
            }

            $this->logActivity($enquiry, 'created', 'Enquiry submitted from website.');

            $this->cart->clear();

            $enquiry->load(['items', 'status']);

            return $enquiry;
        });

        // Notifications run AFTER the enquiry is committed. sendNotifications()
        // already catches and logs its own errors, so a mail failure never
        // affects the saved enquiry — the customer always reaches the thank-you page.
        $this->sendNotifications($enquiry);

        return $enquiry;
    }

    public function updateStatus(Enquiry $enquiry, int $statusId, ?string $note = null): Enquiry
    {
        $status = EnquiryStatus::findOrFail($statusId);
        $old = $enquiry->status?->name;

        $enquiry->update(['enquiry_status_id' => $status->id]);

        $this->logActivity(
            $enquiry,
            'status_changed',
            "Status changed from {$old} to {$status->name}".($note ? ": {$note}" : ''),
            ['from' => $old, 'to' => $status->name]
        );

        return $enquiry->fresh(['status', 'assignee', 'items', 'activities.user']);
    }

    public function assign(Enquiry $enquiry, ?int $userId): Enquiry
    {
        $enquiry->update(['assigned_to' => $userId]);
        $name = $enquiry->fresh()->assignee?->name ?? 'Unassigned';

        $this->logActivity($enquiry, 'assigned', "Enquiry assigned to {$name}.", [
            'assigned_to' => $userId,
        ]);

        return $enquiry->fresh(['status', 'assignee', 'items', 'activities.user']);
    }

    public function addNote(Enquiry $enquiry, string $note): Enquiry
    {
        $notes = trim(($enquiry->internal_notes ? $enquiry->internal_notes."\n\n" : '').$note);
        $enquiry->update(['internal_notes' => $notes]);

        $this->logActivity($enquiry, 'note_added', $note);

        return $enquiry->fresh(['status', 'assignee', 'items', 'activities.user']);
    }

    public function logActivity(Enquiry $enquiry, string $action, ?string $description = null, array $meta = []): void
    {
        EnquiryActivity::create([
            'enquiry_id' => $enquiry->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'meta' => $meta ?: null,
        ]);
    }

    protected function generateNumber(): string
    {
        do {
            $number = 'ENQ-'.now()->format('Ymd').'-'.strtoupper(Str::random(5));
        } while (Enquiry::where('enquiry_number', $number)->exists());

        return $number;
    }

    protected function sendNotifications(Enquiry $enquiry): void
    {
        try {
            $this->smtp->apply();

            $rows = $enquiry->items->map(function ($item) {
                $meta = [];
                if ($item->variation_label) {
                    $meta[] = e($item->variation_label);
                }
                if ($item->product_sku) {
                    $meta[] = 'SKU: '.e($item->product_sku);
                }
                $metaHtml = $meta
                    ? '<br><span style="font-size:12px;color:#8a8098;">'.implode(' &middot; ', $meta).'</span>'
                    : '';

                return '<tr>'
                    .'<td style="padding:12px 16px;font-size:14px;color:#17101f;border-bottom:1px solid #f4effa;">'.e($item->product_title).$metaHtml.'</td>'
                    .'<td align="center" style="padding:12px 16px;font-size:14px;color:#17101f;font-weight:700;border-bottom:1px solid #f4effa;">'.(int) $item->qty.'</td>'
                    .'</tr>';
            })->implode('');

            $totalUnits = (int) $enquiry->items->sum('qty');

            $productsTable =
                '<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border:1px solid #ece7f2;border-radius:12px;overflow:hidden;">'
                .'<tr style="background:#faf7fd;">'
                .'<td style="padding:11px 16px;font-size:11px;font-weight:700;color:#17101f;letter-spacing:.5px;text-transform:uppercase;border-bottom:1px solid #ece7f2;">Product</td>'
                .'<td align="center" style="padding:11px 16px;font-size:11px;font-weight:700;color:#17101f;letter-spacing:.5px;text-transform:uppercase;border-bottom:1px solid #ece7f2;">Qty</td>'
                .'</tr>'
                .$rows
                .'<tr style="background:#faf7fd;">'
                .'<td style="padding:12px 16px;font-size:13px;color:#8a8098;font-weight:600;">Total</td>'
                .'<td align="center" style="padding:12px 16px;font-size:14px;color:#e6007e;font-weight:700;">'.$totalUnits.' unit'.($totalUnits === 1 ? '' : 's').'</td>'
                .'</tr>'
                .'</table>';

            $variables = [
                'customer_name' => $enquiry->name,
                'enquiry_id' => $enquiry->enquiry_number,
                'customer_email' => $enquiry->email,
                'customer_phone' => $enquiry->phone ?: '—',
                'enquiry_status' => $enquiry->status?->name ?? 'Pending',
                'enquiry_date' => optional($enquiry->created_at)->format('d M Y, h:i A'),
                'products' => $productsTable,
                'enquiry_url' => url('/admin/enquiries/'.$enquiry->id),
                'company' => $enquiry->company ?: '—',
                'message' => $enquiry->message ?: '—',
            ];

            $customerMail = $this->mailTemplates->build('customer_enquiry_confirmation', $variables);
            if ($customerMail) {
                Mail::to($enquiry->email)->send(new TemplatedMail($customerMail['subject'], $customerMail['body']));
            }

            $adminEmail = SmtpSetting::current()?->admin_email
                ?: \App\Models\WebsiteSetting::getValue('admin_notification_email');

            if ($adminEmail) {
                $adminMail = $this->mailTemplates->build('admin_enquiry_notification', $variables);
                if ($adminMail) {
                    Mail::to($adminEmail)->send(new TemplatedMail($adminMail['subject'], $adminMail['body']));
                }
            }
        } catch (\Throwable $e) {
            Log::error('Enquiry notification failed', [
                'enquiry_id' => $enquiry->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
