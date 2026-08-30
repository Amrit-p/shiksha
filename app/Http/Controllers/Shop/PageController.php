<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

    public function contactSubmit(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:30',
            'message' => 'required|string|max:2000',
        ]);

        $adminEmail = WebsiteSetting::getValue('admin_notification_email')
            ?: WebsiteSetting::getValue('contact_email');

        try {
            if ($adminEmail) {
                Mail::raw(
                    "Contact form message\n\nName: {$data['name']}\nEmail: {$data['email']}\nPhone: ".($data['phone'] ?? '-')."\n\n{$data['message']}",
                    function ($message) use ($adminEmail, $data) {
                        $message->to($adminEmail)
                            ->subject('Website contact from '.$data['name']);
                    }
                );
            }
        } catch (\Throwable $e) {
            Log::warning('Contact form mail failed: '.$e->getMessage());
        }

        return back()->with('success', 'Thank you. We have received your message.');
    }
}
