<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $templates = [
            'user_welcome_set_password' => [
                'name' => 'User Welcome / Set Password',
                'subject' => 'Welcome to Shiksha Lighting, {{name}} - set your password',
                'body' => self::shell(
                    'Welcome aboard, {{name}}',
                    'An account has been created for you as <strong>{{role}}</strong>. Click the button below to choose your password. The link is valid for {{expires_in}}.',
                    '<table width="100%" cellpadding="0" cellspacing="0" style="background:#faf7fd;border:1px solid #ece7f2;border-radius:12px;margin:0 0 24px;"><tr><td style="padding:14px 18px;font-size:12px;color:#8a8098;">Login email<br><span style="font-size:15px;color:#17101f;font-weight:700;">{{email}}</span></td></tr></table>',
                    '{{set_password_url}}',
                    'Set my password &rarr;',
                    'After setting your password you can sign in at {{login_url}}'
                ),
            ],
            'password_reset_link' => [
                'name' => 'Password Reset Link',
                'subject' => 'Reset your Shiksha Lighting password',
                'body' => self::shell(
                    'Reset your password',
                    'Hi {{name}}, we received a request to reset the password for {{email}}. The link is valid for {{expires_in}}.',
                    '',
                    '{{reset_url}}',
                    'Reset password &rarr;',
                    'If you did not request this, you can ignore this email. Your password will not change.'
                ),
            ],
            'password_changed' => [
                'name' => 'Password Changed Confirmation',
                'subject' => 'Your Shiksha Lighting password was changed',
                'body' => self::shell(
                    'Password changed',
                    'Hi {{name}}, the password for {{email}} was changed on {{changed_at}}.',
                    '',
                    '{{login_url}}',
                    'Sign in &rarr;',
                    'If you did not make this change, reset your password immediately or contact the administrator.'
                ),
            ],
        ];

        foreach ($templates as $slug => $template) {
            // Keep any edits already made in Admin > Email Templates.
            if (DB::table('email_templates')->where('slug', $slug)->exists()) {
                continue;
            }

            DB::table('email_templates')->insert($template + [
                'slug' => $slug,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('email_templates')
            ->whereIn('slug', ['user_welcome_set_password', 'password_reset_link', 'password_changed'])
            ->delete();
    }

    private static function shell(string $title, string $intro, string $extra, string $url, string $button, string $footnote): string
    {
        return <<<HTML
<div style="margin:0;padding:0;background:#f4effa;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4effa;padding:24px 12px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;font-family:Arial,Helvetica,sans-serif;">
<tr><td style="background:#17101f;background:linear-gradient(120deg,#241a33 0%,#17101f 60%,#0d1424 100%);padding:26px 32px;">
<span style="color:#ffffff;font-size:22px;font-weight:700;letter-spacing:.3px;">Shiksha Lighting</span>
</td></tr>
<tr><td style="padding:32px;">
<p style="margin:0 0 6px;font-size:18px;color:#17101f;font-weight:700;">{$title}</p>
<p style="margin:0 0 22px;font-size:14px;line-height:1.7;color:#5b5169;">{$intro}</p>
{$extra}
<table width="100%" cellpadding="0" cellspacing="0"><tr><td align="center" style="padding:8px 0 4px;">
<a href="{$url}" style="display:inline-block;background:#e6007e;color:#ffffff;text-decoration:none;font-size:14px;font-weight:700;padding:13px 28px;border-radius:10px;">{$button}</a>
</td></tr></table>
<p style="margin:22px 0 0;font-size:12px;line-height:1.6;color:#8a8098;">{$footnote}</p>
</td></tr>
<tr><td style="background:#17101f;padding:22px 32px;">
<p style="margin:0;color:rgba(255,255,255,.6);font-size:12px;line-height:1.6;">Automated email from Shiksha Lighting.</p>
</td></tr>
</table>
</td></tr>
</table>
</div>
HTML;
    }
};
