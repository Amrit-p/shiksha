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

        $selectedCategory = null;
        if ($category = $request->get('category')) {
            $selectedCategory = Category::query()
                ->where('id', $category)
                ->orWhere('slug', $category)
                ->first();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('shop.products._grid', compact('products'))->render(),
                'pagination' => view('shop.products._pagination', compact('products'))->render(),
                'count' => $products->total(),
                'categoryName' => $selectedCategory?->name,
            ]);
        }

        $categories = Category::query()
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view('shop.products.index', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
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
                'variants' => fn ($q) => $q->where('status', 1)->with([
                    'attributes.attribute',
                    'attributes.option.attribute',
                    'variantname',
                    'productCodeType',
                    'customFields.customFieldType',
                    'images',
                ]),
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

        return view('shop.products.show', array_merge([
            'product' => $product,
            'related' => $related,
            'metaTitle' => $product->title.' | '.WebsiteSetting::getValue('website_name', 'Shiksha'),
            'metaDescription' => $product->short_description ?: strip_tags((string) $product->description),
        ], $this->variantSelectionData($product)));
    }

    /**
     * Build the cascading variation selector payload for the public product page.
     *
     * Mirrors the salesman frontend cascade (code type -> variant -> custom fields
     * -> attribute option) but deliberately carries no pricing: the customer site
     * quotes on enquiry, so mrp / sell_price / POR flags are never exposed.
     *
     * @return array{variantsJson: \Illuminate\Support\Collection, hasCodeTypes: bool, codeTypes: array, disabledAttributeIds: array, hasVariantSelection: bool}
     */
    protected function variantSelectionData(Product $product): array
    {
        $fallbackImage = $product->image ? asset('storage/'.$product->image) : asset('theme/img/logo.png');

        $variantsJson = $product->variants->map(function ($variant) use ($fallbackImage) {
            return [
                'id' => $variant->id,
                'variantname_id' => $variant->variantname_id ?? null,
                'variant_name' => $variant->variantname->name ?? $variant->name ?? 'Variant',
                'product_code_type_id' => $variant->product_code_type_id,
                'image' => $variant->image ? asset('storage/'.$variant->image) : $fallbackImage,
                'sku' => $variant->sku ?: $variant->catalog_number,
                'stock' => (int) ($variant->stock ?? 0),
                'variant_description' => $variant->variant_description ?? '',
                'custom_fields' => $variant->customFields->map(fn ($field) => [
                    'type_id' => $field->custom_field_type_id,
                    'type_name' => $field->customFieldType->name ?? 'Option',
                    'value' => $field->field_value,
                ])->values(),
                // Label deliberately comes from the attribute name only. In this
                // catalogue attribute_options.name is used as a free-text field that
                // frequently holds price strings ("2500.00 (250 RFT)"), so it must
                // never reach the customer site.
                'attributes' => $variant->attributes->map(fn ($attribute) => [
                    'id' => $attribute->id,
                    'label' => $attribute->attribute?->name
                        ?: ($attribute->option?->attribute?->name ?: 'Option'),
                    'stock' => (int) ($attribute->stock ?? 0),
                    'image' => $attribute->image ? asset('storage/'.$attribute->image) : '',
                ])->values(),
            ];
        })->values();

        $hasCodeTypes = $product->variants->whereNotNull('product_code_type_id')->isNotEmpty();

        $codeTypes = [];
        if ($hasCodeTypes) {
            foreach ($product->variants->whereNotNull('product_code_type_id')->groupBy('product_code_type_id') as $codeTypeId => $variants) {
                if (! $variants->first()?->productCodeType) {
                    continue;
                }

                $codeTypes[] = [
                    'id' => $codeTypeId,
                    'name' => $variants->first()->productCodeType->name,
                    'variant_ids' => $variants->pluck('id')->unique()->values()->all(),
                ];
            }
        }

        $disabledAttributeIds = $product->variants
            ->flatMap->attributes
            ->filter(fn ($attribute) => (int) ($attribute->stock ?? 0) <= 0)
            ->pluck('id')
            ->values()
            ->all();

        return [
            'variantsJson' => $variantsJson,
            'hasCodeTypes' => $hasCodeTypes,
            'codeTypes' => $codeTypes,
            'disabledAttributeIds' => $disabledAttributeIds,
            'hasVariantSelection' => $variantsJson->isNotEmpty(),
        ];
    }

    public function search(Request $request): View|JsonResponse
    {
        $request->merge(['q' => $request->get('q', $request->get('search'))]);

        return $this->index($request);
    }

    /**
     * Lightweight JSON autocomplete for the header search overlay.
     */
    public function suggest(Request $request): JsonResponse
    {
        $term = trim((string) $request->get('q'));

        if (mb_strlen($term) < 2) {
            return response()->json(['results' => [], 'more' => false, 'total' => 0]);
        }

        $base = Product::query()
            ->where('status', 1)
            ->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('slug', 'like', "%{$term}%")
                    ->orWhere('short_description', 'like', "%{$term}%")
                    ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$term}%"));
            });

        $total = (clone $base)->count();

        $products = $base
            ->with('category:id,name')
            ->latest()
            ->take(6)
            ->get(['id', 'title', 'slug', 'image', 'category_id']);

        $results = $products->map(fn ($p) => [
            'title' => $p->title,
            'category' => $p->category->name ?? 'Product',
            'url' => route('shop.product', $p->slug),
            'image' => $p->image ? asset('storage/'.$p->image) : asset('theme/img/logo.png'),
        ]);

        return response()->json([
            'results' => $results,
            'total' => $total,
            'more' => $total > $results->count(),
            'allUrl' => route('shop.search', ['q' => $term]),
        ]);
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
