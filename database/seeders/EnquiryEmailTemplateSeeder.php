<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EnquiryEmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $customerBody = <<<'HTML'
<div style="margin:0;padding:0;background:#f4effa;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4effa;padding:24px 12px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;font-family:Arial,Helvetica,sans-serif;">
<tr><td style="background:#e6007e;background:linear-gradient(120deg,#e6007e 0%,#b23bb0 55%,#0089cf 100%);padding:26px 32px;">
<table width="100%"><tr>
<td style="color:#ffffff;font-size:22px;font-weight:700;letter-spacing:.3px;">Shiksha Lighting</td>
<td align="right" style="color:rgba(255,255,255,.85);font-size:12px;">Enquiry received</td>
</tr></table>
</td></tr>
<tr><td style="padding:32px;">
<p style="margin:0 0 6px;font-size:18px;color:#17101f;font-weight:700;">Hi {{customer_name}},</p>
<p style="margin:0 0 22px;font-size:14px;line-height:1.7;color:#5b5169;">Thank you for your enquiry. We have received your request and our team will reply with pricing and availability &mdash; usually within one business day.</p>
<table width="100%" cellpadding="0" cellspacing="0" style="background:#faf7fd;border:1px solid #ece7f2;border-radius:12px;margin:0 0 24px;">
<tr>
<td style="padding:14px 18px;font-size:11px;color:#8a8098;letter-spacing:.5px;">ENQUIRY NUMBER<br><span style="font-size:17px;color:#e6007e;font-weight:700;letter-spacing:0;">{{enquiry_id}}</span></td>
<td align="right" style="padding:14px 18px;font-size:11px;color:#8a8098;letter-spacing:.5px;">DATE<br><span style="font-size:14px;color:#17101f;font-weight:600;letter-spacing:0;">{{enquiry_date}}</span></td>
</tr>
</table>
<p style="margin:0 0 12px;font-size:12px;color:#17101f;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Products you enquired about</p>
{{products}}
<table width="100%" cellpadding="0" cellspacing="0" style="margin:26px 0 0;">
<tr><td style="border-top:1px solid #ece7f2;padding-top:20px;font-size:13px;line-height:1.7;color:#5b5169;">
<strong style="color:#17101f;">What happens next?</strong><br>Our sales desk will confirm specifications and stock, then send an itemised quote with GST. There is no obligation to buy.
</td></tr>
</table>
</td></tr>
<tr><td style="background:#17101f;padding:22px 32px;">
<p style="margin:0 0 4px;color:#ffffff;font-size:14px;font-weight:700;">Shiksha Lighting</p>
<p style="margin:0;color:rgba(255,255,255,.6);font-size:12px;line-height:1.6;">LED lighting, fans, geysers &amp; electricals &middot; Pan-India dispatch<br>Reply to this email to reach our team directly.</p>
</td></tr>
</table>
</td></tr>
</table>
</div>
HTML;

        $adminBody = <<<'HTML'
<div style="margin:0;padding:0;background:#f4effa;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4effa;padding:24px 12px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;font-family:Arial,Helvetica,sans-serif;">
<tr><td style="background:#17101f;background:linear-gradient(120deg,#241a33 0%,#17101f 60%,#0d1424 100%);padding:26px 32px;">
<table width="100%"><tr>
<td style="color:#ffffff;font-size:22px;font-weight:700;letter-spacing:.3px;">Shiksha Lighting</td>
<td align="right" style="color:rgba(255,255,255,.7);font-size:12px;">New enquiry</td>
</tr></table>
</td></tr>
<tr><td style="padding:32px;">
<p style="margin:0 0 6px;font-size:18px;color:#17101f;font-weight:700;">New enquiry received</p>
<p style="margin:0 0 22px;font-size:14px;line-height:1.7;color:#5b5169;">A customer has submitted an enquiry through the website. Details are below.</p>
<table width="100%" cellpadding="0" cellspacing="0" style="background:#faf7fd;border:1px solid #ece7f2;border-radius:12px;margin:0 0 24px;">
<tr>
<td style="padding:14px 18px;font-size:11px;color:#8a8098;letter-spacing:.5px;">ENQUIRY NUMBER<br><span style="font-size:17px;color:#e6007e;font-weight:700;letter-spacing:0;">{{enquiry_id}}</span></td>
<td align="right" style="padding:14px 18px;font-size:11px;color:#8a8098;letter-spacing:.5px;">DATE<br><span style="font-size:14px;color:#17101f;font-weight:600;letter-spacing:0;">{{enquiry_date}}</span></td>
</tr>
</table>
<p style="margin:0 0 12px;font-size:12px;color:#17101f;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Customer details</p>
<table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #ece7f2;border-radius:12px;border-collapse:separate;overflow:hidden;margin:0 0 24px;">
<tr><td style="width:110px;padding:11px 16px;font-size:12px;color:#8a8098;background:#faf7fd;border-bottom:1px solid #ece7f2;">Name</td><td style="padding:11px 16px;font-size:14px;color:#17101f;border-bottom:1px solid #ece7f2;">{{customer_name}}</td></tr>
<tr><td style="padding:11px 16px;font-size:12px;color:#8a8098;background:#faf7fd;border-bottom:1px solid #ece7f2;">Email</td><td style="padding:11px 16px;font-size:14px;color:#17101f;border-bottom:1px solid #ece7f2;">{{customer_email}}</td></tr>
<tr><td style="padding:11px 16px;font-size:12px;color:#8a8098;background:#faf7fd;border-bottom:1px solid #ece7f2;">Phone</td><td style="padding:11px 16px;font-size:14px;color:#17101f;border-bottom:1px solid #ece7f2;">{{customer_phone}}</td></tr>
<tr><td style="padding:11px 16px;font-size:12px;color:#8a8098;background:#faf7fd;border-bottom:1px solid #ece7f2;">Company</td><td style="padding:11px 16px;font-size:14px;color:#17101f;border-bottom:1px solid #ece7f2;">{{company}}</td></tr>
<tr><td style="padding:11px 16px;font-size:12px;color:#8a8098;background:#faf7fd;">Note</td><td style="padding:11px 16px;font-size:14px;color:#17101f;">{{message}}</td></tr>
</table>
<p style="margin:0 0 12px;font-size:12px;color:#17101f;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Products</p>
{{products}}
<table width="100%" cellpadding="0" cellspacing="0"><tr><td align="center" style="padding:28px 0 4px;">
<a href="{{enquiry_url}}" style="display:inline-block;background:#e6007e;color:#ffffff;text-decoration:none;font-size:14px;font-weight:700;padding:13px 28px;border-radius:10px;">View enquiry in admin &rarr;</a>
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

        EmailTemplate::updateOrCreate(['slug' => 'customer_enquiry_confirmation'], [
            'name' => 'Customer Enquiry Confirmation',
            'subject' => 'We received your enquiry {{enquiry_id}}',
            'body' => $customerBody,
            'is_active' => true,
        ]);

        EmailTemplate::updateOrCreate(['slug' => 'admin_enquiry_notification'], [
            'name' => 'Admin Enquiry Notification',
            'subject' => 'New enquiry {{enquiry_id}} from {{customer_name}}',
            'body' => $adminBody,
            'is_active' => true,
        ]);
    }
}
