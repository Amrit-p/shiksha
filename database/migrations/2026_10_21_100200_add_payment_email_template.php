<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Keep any edits already made in Admin > Email Templates.
        if (DB::table('email_templates')->where('slug', 'admin_payment_logged')->exists()) {
            return;
        }

        DB::table('email_templates')->insert([
            'slug' => 'admin_payment_logged',
            'name' => 'Admin: Payment Logged by Salesman',
            'subject' => '{{salesman_name}} logged a payment of Rs. {{amount}} for order #{{order_id}}',
            'body' => self::shell(
                'Payment awaiting verification',
                '<strong>{{salesman_name}}</strong> recorded a payment of <strong>Rs. {{amount}}</strong> from {{shop_name}} for order #{{order_id}}.',
                '<table width="100%" cellpadding="0" cellspacing="0" style="background:#faf7fd;border:1px solid #ece7f2;border-radius:12px;margin:0 0 24px;"><tr><td style="padding:14px 18px;font-size:13px;line-height:1.8;color:#5b5169;">Received on: <strong style="color:#17101f;">{{paid_on}}</strong><br>Mode: <strong style="color:#17101f;">{{mode}}</strong><br>Reference: <strong style="color:#17101f;">{{reference}}</strong><br>Order total: <strong style="color:#17101f;">Rs. {{order_total}}</strong> &middot; Balance after this: <strong style="color:#17101f;">Rs. {{balance}}</strong></td></tr></table>',
                '{{review_url}}',
                'Review payment &rarr;',
                'The payment counts as collection only after you verify it.'
            ),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('email_templates')->where('slug', 'admin_payment_logged')->delete();
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
