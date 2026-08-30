<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title'             => 'required|string|max:255|unique:products,title',
            'category_id'       => 'required|integer|exists:categories,id',
            'product_type'      => 'required|in:1,3', // 1 = simple, 3 = variant
            'short_description' => 'required|string|max:255',
            'description'       => 'nullable|string',
            'unit_id'           => 'required|integer|exists:units,id',

            'images'           => 'required|image|mimes:jpg,jpeg,png,webp|max:2048|dimensions:width=600,height=600',
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

            $rules['variants']                          = 'required|array|min:1';
            $rules['variants.*.variant_name_id']        = 'required|integer|exists:variant_names,id';
            $rules['variants.*.stock']                  = 'nullable|integer|min:0';
            $rules['variants.*.image']                  = 'required|image|mimes:jpg,jpeg,png,webp|max:2048|dimensions:width=600,height=600';
            $rules['variants.*.product_code_type_id']   = 'nullable|integer|exists:product_code_types,id';
            $rules['variants.*.attributes']             = 'required|array|min:1';
            $rules['variants.*.attributes.*.attribute_id']        = 'required|integer|exists:attributes,id';
            $rules['variants.*.attributes.*.attribute_option_id'] = 'required|integer|exists:attribute_options,id';
            $rules['variants.*.attributes.*.sell_price']          = 'nullable|numeric|min:0';
            $rules['variants.*.attributes.*.stock']               = 'nullable|integer|min:0';
            $rules['variants.*.attributes.*.min_stock']           = 'nullable|integer|min:0';
            $rules['variants.*.attributes.*.image']               = 'required|image|mimes:jpg,jpeg,png,webp|max:2048|dimensions:width=600,height=600';

            // ✅ POR-AWARE MRP VALIDATION
            // Loop through every submitted attribute and check its is_por flag.
            // If is_por == 1 → MRP is nullable (price on request).
            // If is_por != 1 → MRP is required.
            foreach ((array) $this->input('variants', []) as $vIndex => $variant) {
                foreach ((array) ($variant['attributes'] ?? []) as $aIndex => $attr) {
                    $isPor = isset($attr['is_por']) && (int) $attr['is_por'] === 1;

                    $rules["variants.{$vIndex}.attributes.{$aIndex}.mrp"] = $isPor
                        ? 'nullable|numeric|min:0'   // POR → MRP not required
                        : 'required|numeric|min:0';  // Normal → MRP required
                }
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.unique' => 'Product with this title already exists',
            'title.required' => 'Product title is required',
            'category_id.required' => 'Category is required',
            'product_type.required' => 'Product type is required',
            'unit_id.required' => 'Unit is required',


            /* SIMPLE */
            'simple.price.required'     => 'Simple product price is required',

            /* VARIANT */
            'variants.required'         => 'At least one variant is required',

            'variants.*.variant_name_id.required'  => 'Variant type is required',
            'variants.*.image.required'            => 'Variant image is required',
            'variants.*.image.dimensions'          => 'Variant image must be exactly 600×600 pixels',

            'variants.*.product_code_type_id.exists' => 'Selected product code type is invalid',

            'variants.*.attributes.required'                          => 'Each variant must have at least one attribute',
            'variants.*.attributes.*.attribute_id.required'           => 'Attribute is required',
            'variants.*.attributes.*.attribute_option_id.required'    => 'Attribute option is required',
            'variants.*.attributes.*.mrp.required'                    => 'Attribute MRP is required (or enable Price on Request)',
            'variants.*.attributes.*.image.required'                  => 'Attribute image is required',
            'variants.*.attributes.*.image.dimensions'                => 'Attribute image must be exactly 600×600 pixels',

            'images.dimensions'         => 'Product image must be exactly 600×600 pixels',
        ];
    }
}