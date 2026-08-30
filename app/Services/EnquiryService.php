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

        return DB::transaction(function () use ($customerData, $items, $pending, $ip, $userAgent) {
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

            $this->sendNotifications($enquiry);

            return $enquiry;
        });
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

            $productsHtml = $enquiry->items->map(function ($item) {
                $label = e($item->product_title);
                if ($item->variation_label) {
                    $label .= ' ('.e($item->variation_label).')';
                }

                return '<li>'.$label.' × '.(int) $item->qty.'</li>';
            })->implode('');

            $variables = [
                'customer_name' => $enquiry->name,
                'enquiry_id' => $enquiry->enquiry_number,
                'customer_email' => $enquiry->email,
                'customer_phone' => $enquiry->phone,
                'enquiry_status' => $enquiry->status?->name ?? 'Pending',
                'products' => '<ul>'.$productsHtml.'</ul>',
                'enquiry_url' => url('/admin/enquiries/'.$enquiry->id),
                'company' => $enquiry->company ?? '',
                'message' => $enquiry->message ?? '',
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
