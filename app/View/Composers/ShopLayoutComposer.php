<?php

namespace App\View\Composers;

use App\Models\CmsPage;
use App\Models\WebsiteSetting;
use App\Services\CustomerCartService;
use Illuminate\View\View;

class ShopLayoutComposer
{
    public function __construct(protected CustomerCartService $cart) {}

    public function compose(View $view): void
    {
        $logo = WebsiteSetting::getValue('logo', 'front_assets/img/logo.png');
        $logoUrl = str_starts_with((string) $logo, 'http')
            ? $logo
            : asset($logo);

        $view->with([
            'shopSettings' => [
                'name' => WebsiteSetting::getValue('website_name', 'Shiksha'),
                'title' => WebsiteSetting::getValue('website_title', 'Shiksha'),
                'description' => WebsiteSetting::getValue('website_description'),
                'phone' => WebsiteSetting::getValue('contact_phone'),
                'email' => WebsiteSetting::getValue('contact_email'),
                'address' => WebsiteSetting::getValue('address'),
                'primary' => WebsiteSetting::getValue('primary_color', '#E6007E'),
                'secondary' => WebsiteSetting::getValue('secondary_color', '#00ADEF'),
                'button' => WebsiteSetting::getValue('button_color', '#E6007E'),
                'logo' => $logoUrl,
                'favicon' => WebsiteSetting::getValue('favicon'),
                'facebook' => WebsiteSetting::getValue('facebook'),
                'instagram' => WebsiteSetting::getValue('instagram'),
                'youtube' => WebsiteSetting::getValue('youtube'),
                'linkedin' => WebsiteSetting::getValue('linkedin'),
                'keywords' => WebsiteSetting::getValue('meta_keywords'),
            ],
            'shopCartCount' => $this->cart->count(),
            'footerPages' => CmsPage::footer()->get(),
        ]);
    }
}
