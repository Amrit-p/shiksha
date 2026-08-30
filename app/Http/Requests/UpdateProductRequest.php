<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Remove empty attribute rows so we only validate rows that have at least
     * attribute_id set. This prevents "Attribute option is required" on placeholder
     * rows when the user has added a new attribute and filled both attribute and option.
     */
    protected function prepareForValidation(): void
    {
        if ((int) $this->input('product_type') !== 3) {
            return;
        }

        $variants = $this->input('variants', []);
        if (!is_array($variants)) {
            return;
        }

        foreach ($variants as $vIndex => $variant) {
            $attributes = $variant['attributes'] ?? [];
            if (!is_array($attributes)) {
                continue;
            }

            // Debug: log attribute count before filter
            \Log::info('UpdateProductRequest: variant ' . $vIndex . ' attributes BEFORE filter', [
                'count' => count($attributes),
                'rows'  => array_map(function ($a, $i) {
                    return ['index' => $i, 'attribute_id' => $a['attribute_id'] ?? 'missing', 'attribute_option_id' => $a['attribute_option_id'] ?? 'missing', 'id' => $a['id'] ?? 'new'];
                }, $attributes, array_keys($attributes)),
            ]);

            // Keep only rows that have attribute_id set (user actually chose an attribute)
            $filtered = array_values(array_filter($attributes, function ($attr) {
                $id = $attr['attribute_id'] ?? null;
                return $id !== null && $id !== '';
            }));

            \Log::info('UpdateProductRequest: variant ' . $vIndex . ' attributes AFTER filter', ['count' => count($filtered)]);

            $variants[$vIndex]['attributes'] = $filtered;
        }

        // Remove variants that have zero attributes (e.g. new variant card with no attribute rows filled)
        $variants = array_values(array_filter($variants, function ($v) {
            $attrs = $v['attributes'] ?? [];
            return is_array($attrs) && count($attrs) > 0;
        }));

        $this->merge(['variants' => $variants]);
    }

    public function rules(): array
    {  
        $productId = $this->id; // route param

        $rules = [
             'title' => [
                'required',
                'string',
                'max:255',
                // IMPORTANT: ignore current product slug
                Rule::unique('products', 'slug')->ignore($productId),
            ],
            'category_id'       => 'required|integer|exists:categories,id',
            'product_type'      => 'required|in:1,3',
            'short_description' => 'required|string|max:255',
            'description'       => 'nullable|string',
            'unit_id'           => 'required|integer|exists:units,id',

            // Main image optional on update (already exists)
            'images'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048|dimensions:width=600,height=600',
            'product_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048|dimensions:width=600,height=600',
        ];

        /* ================= SIMPLE PRODUCT ================= */
        if ((int) $this->product_type === 1) {
            $rules['simple.price'] = 'required|numeric|min:0';
            $rules['simple.stock'] = 'nullable|integer|min:0';
            $rules['simple.min_stock'] = 'nullable|integer|min:0';
        }

        /* ================= VARIANT PRODUCT ================= */
        if ((int) $this->product_type === 3) {

            $rules['variants']                        = 'required|array|min:1';
            $rules['variants.*.variant_name_id']      = 'required|integer|exists:variant_names,id';
            $rules['variants.*.stock']                = 'nullable|integer|min:0';
            $rules['variants.*.product_code_type_id'] = 'nullable|integer|exists:product_code_types,id';
            $rules['variants.*.attributes']           = 'required|array|min:1';

            $rules['variants.*.attributes.*.attribute_id']        = 'required|integer|exists:attributes,id';
            // attribute_option_id required only when attribute_id is present (avoids false errors on new rows)
            $rules['variants.*.attributes.*.attribute_option_id'] = 'nullable|integer|exists:attribute_options,id';
            $rules['variants.*.attributes.*.sell_price']          = 'nullable|numeric|min:0';
            $rules['variants.*.attributes.*.stock']                = 'nullable|integer|min:0';
            $rules['variants.*.attributes.*.min_stock']            = 'nullable|integer|min:0';

            // Images optional on update (existing image kept if no new file)
            $rules['variants.*.image']               = 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048|dimensions:width=600,height=600';
            $rules['variants.*.attributes.*.image']  = 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048|dimensions:width=600,height=600';

            // ✅ POR-AWARE MRP VALIDATION + attribute_option_id when attribute_id present
            foreach ((array) $this->input('variants', []) as $vIndex => $variant) {
                foreach ((array) ($variant['attributes'] ?? []) as $aIndex => $attr) {
                    $isPor = isset($attr['is_por']) && (int) $attr['is_por'] === 1;

                    $rules["variants.{$vIndex}.attributes.{$aIndex}.mrp"] = $isPor
                        ? 'nullable|numeric|min:0'   // POR → MRP not required
                        : 'required|numeric|min:0';  // Normal → MRP required

                    // Require attribute_option_id only when attribute_id is set (fixes new attribute row validation)
                    $rules["variants.{$vIndex}.attributes.{$aIndex}.attribute_option_id"] = [
                        Rule::requiredIf(fn () => !empty($this->input("variants.{$vIndex}.attributes.{$aIndex}.attribute_id"))),
                        'nullable',
                        'integer',
                        'exists:attribute_options,id',
                    ];
                }
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required'        => 'Product title is required',
            'category_id.required'  => 'Category is required',
            'product_type.required' => 'Product type is required',
            'unit_id.required'      => 'Unit is required',

            /* SIMPLE */
            'simple.price.required' => 'Simple product price is required',

            /* VARIANT */
            'variants.required'     => 'At least one variant is required',

            'variants.*.variant_name_id.required'  => 'Variant type is required',
            'variants.*.image.dimensions'          => 'Variant image must be exactly 600×600 pixels',

            'variants.*.product_code_type_id.exists' => 'Selected product code type is invalid',

            'variants.*.attributes.required'                       => 'Each variant must have at least one attribute',
            'variants.*.attributes.*.attribute_id.required'        => 'Attribute is required',
            'variants.*.attributes.*.attribute_option_id.required' => 'Attribute option is required',
            'variants.*.attributes.*.mrp.required'                 => 'Attribute MRP is required (or enable Price on Request)',
            'variants.*.attributes.*.image.dimensions'             => 'Attribute image must be exactly 600×600 pixels',

            'images.dimensions' => 'Product image must be exactly 600×600 pixels',
        ];
    }
}