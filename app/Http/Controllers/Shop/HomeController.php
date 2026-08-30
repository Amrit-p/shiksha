<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\HomepageSection;
use App\Models\Product;
use App\Models\WebsiteSetting;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $banners = Banner::query()->where('status', 1)->latest()->get();
        $categories = Category::query()
            ->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('category_id')->orWhere('category_id', 0);
            })
            ->orderBy('name')
            ->take(8)
            ->get();

        $featured = Product::query()
            ->with('category')
            ->where('status', 1)
            ->where('is_featured', 1)
            ->latest()
            ->take(8)
            ->get();

        if ($featured->isEmpty()) {
            $featured = Product::query()->with('category')->where('status', 1)->latest()->take(8)->get();
        }

        $popular = Product::query()
            ->with('category')
            ->where('status', 1)
            ->where(function ($q) {
                $q->where('is_tranding', 1)->orWhere('is_featured', 1);
            })
            ->latest()
            ->take(8)
            ->get();

        if ($popular->isEmpty()) {
            $popular = Product::query()->with('category')->where('status', 1)->latest()->take(8)->get();
        }

        $brands = Brand::query()->where('status', 1)->take(12)->get();
        $sections = HomepageSection::active()->get()->keyBy('key');

        return view('shop.home', [
            'banners' => $banners,
            'categories' => $categories,
            'featured' => $featured,
            'popular' => $popular,
            'brands' => $brands,
            'sections' => $sections,
            'settings' => $this->settings(),
        ]);
    }

    public function about(): View
    {
        $section = HomepageSection::byKey('about_preview');

        return view('shop.pages.about', [
            'section' => $section,
            'settings' => $this->settings(),
            'metaTitle' => 'About Us | '.WebsiteSetting::getValue('website_name', 'Shiksha'),
            'metaDescription' => $section?->subtitle,
        ]);
    }

    public function contact(): View
    {
        return view('shop.pages.contact', [
            'settings' => $this->settings(),
            'metaTitle' => 'Contact Us | '.WebsiteSetting::getValue('website_name', 'Shiksha'),
            'metaDescription' => 'Contact Shiksha for product enquiries and support.',
        ]);
    }

    protected function settings(): array
    {
        return [
            'name' => WebsiteSetting::getValue('website_name', 'Shiksha'),
            'phone' => WebsiteSetting::getValue('contact_phone'),
            'email' => WebsiteSetting::getValue('contact_email'),
            'address' => WebsiteSetting::getValue('address'),
            'primary' => WebsiteSetting::getValue('primary_color', '#E6007E'),
            'secondary' => WebsiteSetting::getValue('secondary_color', '#00ADEF'),
            'button' => WebsiteSetting::getValue('button_color', '#E6007E'),
            'logo' => WebsiteSetting::getValue('logo', 'front_assets/img/logo.png'),
            'facebook' => WebsiteSetting::getValue('facebook'),
            'instagram' => WebsiteSetting::getValue('instagram'),
            'youtube' => WebsiteSetting::getValue('youtube'),
            'linkedin' => WebsiteSetting::getValue('linkedin'),
        ];
    }
}
