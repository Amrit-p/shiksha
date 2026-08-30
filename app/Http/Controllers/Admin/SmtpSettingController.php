<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TemplatedMail;
use App\Models\SmtpSetting;
use App\Services\SmtpConfigurator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SmtpSettingController extends Controller
{
    public function edit()
    {
        $smtp = SmtpSetting::current() ?: new SmtpSetting([
            'port' => 587,
            'encryption' => 'tls',
            'is_active' => false,
        ]);

        return view('admin.smtp.edit', compact('smtp'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'host' => 'nullable|string|max:190',
            'port' => 'nullable|integer|min:1|max:65535',
            'username' => 'nullable|string|max:190',
            'password' => 'nullable|string|max:255',
            'encryption' => 'nullable|in:tls,ssl,',
            'from_email' => 'nullable|email|max:190',
            'from_name' => 'nullable|string|max:190',
            'admin_email' => 'nullable|email|max:190',
            'is_active' => 'nullable|boolean',
        ]);

        $smtp = SmtpSetting::current() ?: new SmtpSetting();
        $smtp->fill([
            'host' => $data['host'] ?? null,
            'port' => $data['port'] ?? 587,
            'username' => $data['username'] ?? null,
            'encryption' => $data['encryption'] ?: null,
            'from_email' => $data['from_email'] ?? null,
            'from_name' => $data['from_name'] ?? null,
            'admin_email' => $data['admin_email'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        if (! empty($data['password'])) {
            $smtp->password = $data['password'];
        }

        $smtp->save();

        return back()->with('success', 'SMTP settings saved.');
    }

    public function test(Request $request, SmtpConfigurator $smtpConfigurator)
    {
        $data = $request->validate([
            'test_email' => 'required|email',
        ]);

        $smtp = SmtpSetting::current();
        if (! $smtp || ! $smtpConfigurator->apply($smtp)) {
            return back()->with('error', 'SMTP is not configured or inactive.');
        }

        try {
            Mail::to($data['test_email'])->send(new TemplatedMail(
                'Shiksha SMTP Test',
                '<p>This is a test email from Shiksha SMTP settings.</p>'
            ));
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Test email failed: '.$e->getMessage());
        }

        return back()->with('success', 'Test email sent successfully.');
    }
}
