<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use App\Models\EmailTemplate;
use App\Models\EnquiryStatus;
use App\Models\HomepageSection;
use App\Models\SmtpSetting;
use App\Models\WebsiteSetting;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CustomerWebsiteSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'Pending', 'slug' => 'pending', 'color' => '#f59e0b', 'sort_order' => 1],
            ['name' => 'Contacted', 'slug' => 'contacted', 'color' => '#00ADEF', 'sort_order' => 2],
            ['name' => 'In Progress', 'slug' => 'in-progress', 'color' => '#6366f1', 'sort_order' => 3],
            ['name' => 'Won', 'slug' => 'won', 'color' => '#16a34a', 'sort_order' => 4],
            ['name' => 'Rejected', 'slug' => 'rejected', 'color' => '#dc2626', 'sort_order' => 5],
            ['name' => 'Fake', 'slug' => 'fake', 'color' => '#6b7280', 'sort_order' => 6],
            ['name' => 'Closed', 'slug' => 'closed', 'color' => '#111827', 'sort_order' => 7],
        ];

        foreach ($statuses as $status) {
            EnquiryStatus::updateOrCreate(['slug' => $status['slug']], $status + ['is_active' => true]);
        }

        $settings = [
            ['group' => 'general', 'key' => 'website_name', 'value' => 'Shiksha', 'type' => 'text'],
            ['group' => 'general', 'key' => 'website_title', 'value' => 'Shiksha — Professional Lighting Solutions', 'type' => 'text'],
            ['group' => 'general', 'key' => 'website_description', 'value' => 'Discover quality lighting and electrical products. Browse our catalogue and send an enquiry.', 'type' => 'textarea'],
            ['group' => 'general', 'key' => 'contact_email', 'value' => 'info@shiksha.local', 'type' => 'text'],
            ['group' => 'general', 'key' => 'contact_phone', 'value' => '+91 98765 43210', 'type' => 'text'],
            ['group' => 'general', 'key' => 'address', 'value' => 'India', 'type' => 'textarea'],
            ['group' => 'general', 'key' => 'admin_notification_email', 'value' => 'admin@gmail.com', 'type' => 'text'],
            ['group' => 'branding', 'key' => 'primary_color', 'value' => '#E6007E', 'type' => 'color'],
            ['group' => 'branding', 'key' => 'secondary_color', 'value' => '#00ADEF', 'type' => 'color'],
            ['group' => 'branding', 'key' => 'button_color', 'value' => '#E6007E', 'type' => 'color'],
            ['group' => 'branding', 'key' => 'logo', 'value' => 'front_assets/img/logo.png', 'type' => 'image'],
            ['group' => 'social', 'key' => 'facebook', 'value' => '', 'type' => 'url'],
            ['group' => 'social', 'key' => 'instagram', 'value' => '', 'type' => 'url'],
            ['group' => 'social', 'key' => 'youtube', 'value' => '', 'type' => 'url'],
            ['group' => 'social', 'key' => 'linkedin', 'value' => '', 'type' => 'url'],
            ['group' => 'seo', 'key' => 'meta_keywords', 'value' => 'shiksha, lighting, electrical, enquiry', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            WebsiteSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        HomepageSection::updateOrCreate(['key' => 'hero'], [
            'title' => 'Illuminate Every Space with Shiksha',
            'subtitle' => 'Professional lighting solutions for homes, offices, and commercial projects.',
            'content' => 'Browse our catalogue, shortlist products, and send an enquiry — our team will get back to you shortly.',
            'button_text' => 'Explore Products',
            'button_link' => '/products',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        HomepageSection::updateOrCreate(['key' => 'about_preview'], [
            'title' => 'About Shiksha',
            'subtitle' => 'Quality you can trust',
            'content' => 'Shiksha delivers reliable lighting and electrical products crafted for performance and durability. From residential fittings to commercial solutions, we help you choose the right products with expert support.',
            'button_text' => 'Learn More',
            'button_link' => '/about',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        HomepageSection::updateOrCreate(['key' => 'why_us'], [
            'title' => 'Why Choose Us',
            'subtitle' => 'Built around quality and service',
            'content' => json_encode([
                ['title' => 'Curated Catalogue', 'text' => 'Browse featured products with clear specifications.'],
                ['title' => 'Fast Enquiries', 'text' => 'Shortlist items and submit an enquiry in minutes.'],
                ['title' => 'Expert Support', 'text' => 'Our team helps you finalize the right products.'],
                ['title' => 'Trusted Brand', 'text' => 'Designed around the Shiksha promise of quality.'],
            ]),
            'is_active' => true,
            'sort_order' => 3,
        ]);

        HomepageSection::updateOrCreate(['key' => 'cta'], [
            'title' => 'Ready to start your project?',
            'subtitle' => 'Add products to your enquiry cart and we will contact you.',
            'button_text' => 'Make an Enquiry',
            'button_link' => '/cart',
            'is_active' => true,
            'sort_order' => 4,
        ]);

        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => '<p>Shiksha is committed to delivering high-quality lighting and electrical products with dependable service and support.</p>',
                'show_in_footer' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Terms & Conditions',
                'slug' => 'terms-and-conditions',
                'content' => '<p>Please read these terms carefully before using our website or submitting an enquiry.</p>',
                'show_in_footer' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<p>We respect your privacy. Information submitted through enquiry forms is used only to respond to your request.</p>',
                'show_in_footer' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'Refund & Return Policy',
                'slug' => 'refund-return-policy',
                'content' => '<p>Refund and return terms are confirmed after enquiry review with our sales team.</p>',
                'show_in_footer' => true,
                'sort_order' => 4,
            ],
            [
                'title' => 'Shipping Policy',
                'slug' => 'shipping-policy',
                'content' => '<p>Shipping timelines and charges are shared after your enquiry is reviewed.</p>',
                'show_in_footer' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($pages as $page) {
            CmsPage::updateOrCreate(['slug' => $page['slug']], $page + [
                'is_active' => true,
                'meta_title' => $page['title'].' | Shiksha',
                'meta_description' => strip_tags($page['content']),
            ]);
        }

        EmailTemplate::updateOrCreate(['slug' => 'customer_enquiry_confirmation'], [
            'name' => 'Customer Enquiry Confirmation',
            'subject' => 'We received your enquiry {{enquiry_id}}',
            'body' => '<p>Hi {{customer_name}},</p><p>Thank you for your enquiry <strong>{{enquiry_id}}</strong>.</p><p>Our team will contact you shortly.</p><p><strong>Requested products:</strong></p>{{products}}<p>Regards,<br>Shiksha Team</p>',
            'is_active' => true,
        ]);

        EmailTemplate::updateOrCreate(['slug' => 'admin_enquiry_notification'], [
            'name' => 'Admin Enquiry Notification',
            'subject' => 'New enquiry {{enquiry_id}} from {{customer_name}}',
            'body' => '<p>A new enquiry has been submitted.</p><p><strong>ID:</strong> {{enquiry_id}}<br><strong>Name:</strong> {{customer_name}}<br><strong>Email:</strong> {{customer_email}}<br><strong>Phone:</strong> {{customer_phone}}</p><p><strong>Products:</strong></p>{{products}}<p><a href="{{enquiry_url}}">View enquiry</a></p>',
            'is_active' => true,
        ]);

        if (! SmtpSetting::current()) {
            SmtpSetting::create([
                'host' => '',
                'port' => 587,
                'encryption' => 'tls',
                'from_name' => 'Shiksha',
                'admin_email' => 'admin@gmail.com',
                'is_active' => false,
            ]);
        }

        $permission = Permission::findOrCreate('manage_enquiries', 'web');
        foreach (['Admin', 'Super Admin', 'Sub Admin'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role && ! $role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
            }
        }

        // Ensure Admin role can access admin middleware if missing
        $adminPermission = Permission::findOrCreate('admin', 'web');
        foreach (['Admin', 'Super Admin', 'Sub Admin'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role && ! $role->hasPermissionTo($adminPermission)) {
                $role->givePermissionTo($adminPermission);
            }
        }

        WebsiteSetting::flushCache();
    }
}
