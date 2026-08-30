<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;

class WebsiteSettingController extends Controller
{
    public function edit()
    {
        $settings = WebsiteSetting::query()->get()->keyBy('key');

        return view('admin.website_settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $fields = [
            'website_name' => ['general', 'text'],
            'website_title' => ['general', 'text'],
            'website_description' => ['general', 'textarea'],
            'contact_email' => ['general', 'text'],
            'contact_phone' => ['general', 'text'],
            'address' => ['general', 'textarea'],
            'admin_notification_email' => ['email', 'text'],
            'primary_color' => ['branding', 'color'],
            'secondary_color' => ['branding', 'color'],
            'button_color' => ['branding', 'color'],
            'facebook' => ['social', 'url'],
            'instagram' => ['social', 'url'],
            'youtube' => ['social', 'url'],
            'linkedin' => ['social', 'url'],
            'meta_keywords' => ['seo', 'text'],
        ];

        foreach ($fields as $key => [$group, $type]) {
            if ($request->has($key)) {
                WebsiteSetting::setValue($key, $request->input($key), $group, $type);
            }
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('website', 'public');
            WebsiteSetting::setValue('logo', 'storage/'.$path, 'branding', 'image');
        }

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('website', 'public');
            WebsiteSetting::setValue('favicon', $path, 'branding', 'image');
        }

        return back()->with('success', 'Website settings updated.');
    }
}
