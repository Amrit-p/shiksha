<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CmsPage;
use App\Models\Product;
use App\Models\WebsiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $query = $this->filteredQuery($request);

        $products = $query->paginate(12)->withQueryString();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('shop.products._grid', compact('products'))->render(),
                'pagination' => view('shop.products._pagination', compact('products'))->render(),
                'count' => $products->total(),
            ]);
        }

        $categories = Category::query()
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view('shop.products.index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['q', 'category', 'type', 'min_price', 'max_price', 'availability', 'sort']),
            'metaTitle' => 'Products | '.WebsiteSetting::getValue('website_name', 'Shiksha'),
            'metaDescription' => 'Browse the Shiksha product catalogue.',
        ]);
    }

    public function show(string $slug): View
    {
        $product = Product::query()
            ->with([
                'category',
                'gallary_images',
                'unit',
                'variants' => fn ($q) => $q->where('status', 1)->with(['attributes.attribute', 'attributes.option', 'variantname', 'images']),
                'attributePrices' => fn ($q) => $q->with(['attribute', 'option']),
            ])
            ->where('status', 1)
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Product::query()
            ->with('category')
            ->where('status', 1)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('shop.products.show', [
            'product' => $product,
            'related' => $related,
            'metaTitle' => $product->title.' | '.WebsiteSetting::getValue('website_name', 'Shiksha'),
            'metaDescription' => $product->short_description ?: strip_tags((string) $product->description),
        ]);
    }

    public function search(Request $request): View|JsonResponse
    {
        $request->merge(['q' => $request->get('q', $request->get('search'))]);

        return $this->index($request);
    }

    protected function filteredQuery(Request $request)
    {
        $query = Product::query()
            ->with('category')
            ->where('status', 1);

        if ($search = trim((string) $request->get('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('variants', function ($v) use ($search) {
                        $v->where('sku', 'like', "%{$search}%")
                            ->orWhere('catalog_number', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($category = $request->get('category')) {
            $query->where(function ($q) use ($category) {
                $q->where('category_id', $category)
                    ->orWhereHas('category', fn ($c) => $c->where('slug', $category)->orWhere('id', $category));
            });
        }

        if ($type = $request->get('type')) {
            $query->where('product_type', $type);
        }

        if ($request->filled('min_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('sale_price', '>=', (float) $request->min_price)
                    ->orWhere(function ($q2) use ($request) {
                        $q2->whereNull('sale_price')->where('price', '>=', (float) $request->min_price);
                    });
            });
        }

        if ($request->filled('max_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('sale_price', '<=', (float) $request->max_price)
                    ->orWhere(function ($q2) use ($request) {
                        $q2->whereNull('sale_price')->where('price', '<=', (float) $request->max_price);
                    });
            });
        }

        if ($request->get('availability') === 'in_stock') {
            $query->where(function ($q) {
                $q->where('stock', '>', 0)->orWhereNull('stock');
            });
        }

        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'price_asc' => $query->orderByRaw('COALESCE(sale_price, price) asc'),
            'price_desc' => $query->orderByRaw('COALESCE(sale_price, price) desc'),
            'name' => $query->orderBy('title'),
            default => $query->latest(),
        };

        return $query;
    }
}
