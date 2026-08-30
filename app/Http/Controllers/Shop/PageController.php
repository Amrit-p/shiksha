<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\ContactMessage;
use App\Models\SmtpSetting;
use App\Models\WebsiteSetting;
use App\Services\SmtpConfigurator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $page = CmsPage::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('shop.pages.cms', [
            'page' => $page,
            'metaTitle' => $page->meta_title ?: ($page->title.' | '.WebsiteSetting::getValue('website_name', 'Shiksha')),
            'metaDescription' => $page->meta_description,
            'metaKeywords' => $page->meta_keywords,
        ]);
    }

    public function contactSubmit(Request $request, SmtpConfigurator $smtp)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:30',
            'message' => 'required|string|max:2000',
        ]);

        // Persist the message first, so we always have a record even if mail fails.
        ContactMessage::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'message' => $data['message'],
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 250),
        ]);

        // Same priority as enquiry notifications: SMTP-settings admin email first,
        // then the website-settings values.
        $adminEmail = optional(SmtpSetting::current())->admin_email
            ?: WebsiteSetting::getValue('admin_notification_email')
            ?: WebsiteSetting::getValue('contact_email');

        try {
            // Use the admin SMTP settings (if configured & active) so the email
            // actually delivers; otherwise it falls back to the default mailer.
            $smtp->apply();

            if ($adminEmail) {
                $body = "New contact message from the website\n\n"
                    ."Name: {$data['name']}\n"
                    ."Email: {$data['email']}\n"
                    .'Phone: '.($data['phone'] ?: '-')."\n\n"
                    ."Message:\n{$data['message']}\n";

                Mail::raw($body, function ($message) use ($adminEmail, $data) {
                    $message->to($adminEmail)
                        ->replyTo($data['email'], $data['name'])
                        ->subject('Website contact from '.$data['name']);
                });
            }
        } catch (\Throwable $e) {
            Log::warning('Contact form mail failed: '.$e->getMessage());
        }

        return back()->with('success', 'Thank you. We have received your message.');
    }
}
