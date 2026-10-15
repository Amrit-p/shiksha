<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        // Admin email for credit orders; editable afterwards from Admin > Email Templates.
        DB::table('email_templates')->updateOrInsert(
            ['slug' => 'admin_credit_order_notification'],
            [
                'name' => 'Admin Credit Order Notification',
                'subject' => 'Credit order #{{order_id}} from {{shop_name}} ({{credit_days}} days)',
                'body' => self::adminBody(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('email_templates')->where('slug', 'admin_credit_order_notification')->delete();
        Schema::dropIfExists('notifications');
    }

    private static function adminBody(): string
    {
        return <<<'HTML'
<div style="margin:0;padding:0;background:#f4effa;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4effa;padding:24px 12px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;font-family:Arial,Helvetica,sans-serif;">
<tr><td style="background:#17101f;background:linear-gradient(120deg,#241a33 0%,#17101f 60%,#0d1424 100%);padding:26px 32px;">
<table width="100%"><tr>
<td style="color:#ffffff;font-size:22px;font-weight:700;letter-spacing:.3px;">Shiksha Lighting</td>
<td align="right" style="color:rgba(255,255,255,.7);font-size:12px;">Credit order</td>
</tr></table>
</td></tr>
<tr><td style="padding:32px;">
<p style="margin:0 0 6px;font-size:18px;color:#17101f;font-weight:700;">New credit order placed</p>
<p style="margin:0 0 22px;font-size:14px;line-height:1.7;color:#5b5169;">{{salesman_name}} has punched an order on credit. Please review it before dispatch.</p>
<table width="100%" cellpadding="0" cellspacing="0" style="background:#faf7fd;border:1px solid #ece7f2;border-radius:12px;margin:0 0 24px;">
<tr>
<td style="padding:14px 18px;font-size:11px;color:#8a8098;letter-spacing:.5px;">ORDER<br><span style="font-size:17px;color:#e6007e;font-weight:700;letter-spacing:0;">#{{order_id}}</span></td>
<td style="padding:14px 18px;font-size:11px;color:#8a8098;letter-spacing:.5px;">AMOUNT<br><span style="font-size:17px;color:#17101f;font-weight:700;letter-spacing:0;">&#8377;{{total_amount}}</span></td>
<td align="right" style="padding:14px 18px;font-size:11px;color:#8a8098;letter-spacing:.5px;">CREDIT<br><span style="font-size:17px;color:#17101f;font-weight:700;letter-spacing:0;">{{credit_days}} days</span></td>
</tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #ece7f2;border-radius:12px;border-collapse:separate;overflow:hidden;margin:0 0 24px;">
<tr><td style="width:130px;padding:11px 16px;font-size:12px;color:#8a8098;background:#faf7fd;border-bottom:1px solid #ece7f2;">Shop</td><td style="padding:11px 16px;font-size:14px;color:#17101f;border-bottom:1px solid #ece7f2;">{{shop_name}}</td></tr>
<tr><td style="padding:11px 16px;font-size:12px;color:#8a8098;background:#faf7fd;border-bottom:1px solid #ece7f2;">Owner</td><td style="padding:11px 16px;font-size:14px;color:#17101f;border-bottom:1px solid #ece7f2;">{{owner_name}} &middot; {{owner_phone}}</td></tr>
<tr><td style="padding:11px 16px;font-size:12px;color:#8a8098;background:#faf7fd;border-bottom:1px solid #ece7f2;">City</td><td style="padding:11px 16px;font-size:14px;color:#17101f;border-bottom:1px solid #ece7f2;">{{city}}</td></tr>
<tr><td style="padding:11px 16px;font-size:12px;color:#8a8098;background:#faf7fd;border-bottom:1px solid #ece7f2;">Quantity</td><td style="padding:11px 16px;font-size:14px;color:#17101f;border-bottom:1px solid #ece7f2;">{{total_qty}}</td></tr>
<tr><td style="padding:11px 16px;font-size:12px;color:#8a8098;background:#faf7fd;border-bottom:1px solid #ece7f2;">Payment due</td><td style="padding:11px 16px;font-size:14px;color:#17101f;border-bottom:1px solid #ece7f2;">{{due_date}}</td></tr>
<tr><td style="padding:11px 16px;font-size:12px;color:#8a8098;background:#faf7fd;">Placed on</td><td style="padding:11px 16px;font-size:14px;color:#17101f;">{{order_date}}</td></tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0"><tr><td align="center" style="padding:8px 0 4px;">
<a href="{{order_url}}" style="display:inline-block;background:#e6007e;color:#ffffff;text-decoration:none;font-size:14px;font-weight:700;padding:13px 28px;border-radius:10px;">View order in admin &rarr;</a>
</td></tr></table>
</td></tr>
<tr><td style="background:#17101f;padding:22px 32px;">
<p style="margin:0;color:rgba(255,255,255,.6);font-size:12px;line-height:1.6;">Automated notification from your Shiksha Lighting website.</p>
</td></tr>
</table>
</td></tr>
</table>
</div>
HTML;
    }
};
