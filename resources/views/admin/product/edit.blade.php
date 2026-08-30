@extends('admin.layouts.main')
@section('title', 'Edit Product')

@section('content')
    <style>
        /* sell price error ui fix */
        .variant-attr-row {
            position: relative;
        }

        .sell-price-wrapper {
            position: relative;
        }

        .sell-price-wrapper small.js-sell-error {
            position: absolute;
            bottom: -18px;
            left: 0;
            font-size: 12px;
            white-space: nowrap;
        }

        .sell-price-wrapper input.is-invalid {
            border-color: #dc3545;
        }

        .attr-image-input.is-invalid {
            border-color: #dc3545;
        }

        .attr-image-error {
            display: block;
            font-size: 12px;
        }

        /* ✅ POR styles */
        .por-badge {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .mrp-por-disabled {
            background-color: #e9ecef !important;
            cursor: not-allowed;
        }
    </style>
    <div class="container-fluid">

        <div class="page-header mb-4">
            <h4 class="mb-0">Edit Product</h4>
            <small class="text-muted">Supports Simple & Variant</small>
        </div>

        <div class="card">
            <div class="card-body">

                @if (session('error'))
                    <div class="alert alert-danger mb-4" role="alert">
                        <h6 class="alert-heading mb-2">Update failed</h6>
                        <p class="mb-1"><strong>Error:</strong> {{ session('error') }}</p>
                        @if (session('error_file'))
                            <p class="mb-1 small"><strong>File:</strong> {{ session('error_file') }} (line {{ session('error_line') }})</p>
                        @endif
                        @if (session('error_trace'))
                            <details class="mt-2">
                                <summary class="small text-muted" style="cursor:pointer;">Show stack trace</summary>
                                <pre class="small bg-light p-2 mt-1 mb-0 text-left" style="max-height:200px;overflow:auto;">{{ session('error_trace') }}</pre>
                            </details>
                        @endif
                    </div>
                @endif

                <form action="{{ route('admin.product.update', $product->id) }}" method="POST" enctype="multipart/form-data"
                    id="product-form">
                    @csrf
                    @method('PATCH')

                    {{-- ================= BASIC INFO ================= --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Product Title *</label>
                            <input type="text" name="title" class="form-control"
                                value="{{ old('title', $product->title) }}">
                            @error('title')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold">Category *</label>
                            <select name="category_id" class="form-control">
                                <option value="">Select</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ (int) old('category_id', $product->category_id) === (int) $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Product Type *</label>
                            <select name="product_type" id="product_type" class="form-control" disabled>
                                <option value="1"
                                    {{ (int) old('product_type', $product->product_type) === 1 ? 'selected' : '' }}>Simple
                                </option>
                                <option value="3"
                                    {{ (int) old('product_type', $product->product_type) === 3 ? 'selected' : '' }}>Variant
                                </option>
                            </select>
                            <input type="hidden" name="product_type" value="{{ $product->product_type }}">
                            @error('product_type')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold">Short Description *</label>
                            <input type="text" name="short_description" maxlength="150" class="form-control"
                                value="{{ old('short_description', $product->short_description) }}">
                            <small class="text-muted d-block mt-1"><span
                                    id="char-count">{{ strlen(old('short_description', $product->short_description)) }}</span>/150</small>
                            @error('short_description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- ================= MAIN IMAGE ================= --}}
                    <div class="row mb-4">

                        <div class="col-md-6">
                            <label class="fw-bold">Main Image</label>
                            <input type="file" name="images" class="form-control" accept="image/*">
                            @error('images')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-2 main-image-note">
                                Image size must be <strong>600 × 600</strong> pixels (JPG, PNG)
                            </small>

                            @if ($product->image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $product->image) }}" width="90"
                                        class="img-thumbnail">
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Unit *</label>
                            <select name="unit_id" class="form-control">
                                <option value="">Select Unit</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}"
                                        {{ (int) old('unit_id', $product->unit_id) === (int) $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unit_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- <div class="mb-4">
                                <label class="fw-bold">Main Image</label>
                                <input type="file" name="image" class="form-control">
                                @error('image') <small class="text-danger">{{ $message }}</small> @enderror

                                @if ($product->image && file_exists(storage_path('app/public/' . $product->image)))
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/'.$product->image) }}" width="90" class="img-thumbnail">
                                    </div>
                                @else
                                    <small class="text-muted">Image not found</small>
                                @endif
                            </div> --}}


                        {{-- ================= GALLERY ================= --}}
                        {{-- <div class="col-md-6">
                            <label class="fw-bold">Gallery Images</label>
                            <input type="file" name="product_images[]" multiple class="form-control" accept="image/*">
                            @error('product_images.*')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror

                            @if ($product->gallary_images && $product->gallary_images->count())
                                <div class="d-flex flex-wrap gap-2 mt-3" id="existing-gallery">
                                    @foreach ($product->gallary_images as $img)
                                        <div class="position-relative">
                                            <img src="{{ asset('storage/' . $img->product_images) }}" class="img-thumbnail"
                                                style="width:100px;height:100px;object-fit:cover;">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div> --}}
                    </div>

                    {{-- ================= SIMPLE SECTION ================= --}}
                    <div id="simple-section" class="{{ (int) $product->product_type === 1 ? '' : 'd-none' }}">
                        <hr>
                        <h5 class="mb-3">Simple Product Pricing</h5>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold">MRP *</label>
                                <input type="number" step="0.01" class="form-control" name="simple[price]"
                                    value="{{ old('simple.price', $product->price ?? 0) }}">
                                @error('simple.price')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="fw-bold">Sell Price </label>
                                <input type="number" step="0.01" class="form-control" name="simple[sale_price]"
                                    value="{{ old('simple.sale_price', $product->sale_price ?? 0) }}">
                                @error('simple.sale_price')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="fw-bold">Stock</label>
                                <input type="number" class="form-control" name="simple[stock]"
                                    value="{{ old('simple.stock', $product->stock ?? 0) }}">
                                @error('simple.stock')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold">Min stock (alert when below)</label>
                                <input type="number" min="0" class="form-control" name="simple[min_stock]"
                                    value="{{ old('simple.min_stock', $product->min_stock ?? 0) }}" placeholder="0">
                                <small class="text-muted">Dashboard restock list when stock &lt; this</small>
                            </div>
                        </div>
                    </div>


                    {{-- ✅ CUSTOM FIELD TYPES SELECTION --}}
                    <div id="custom-fields-config-section"
                        class="{{ (int) old('product_type', $product->product_type) === 3 ? '' : 'd-none' }}">
                        <hr class="my-4">
                        <div class="card border-info mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="ik ik-settings mr-2"></i>
                                    Custom Selection Fields (Optional)
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-light">
                                    <i class="ik ik-info mr-2"></i>
                                    Add extra selection fields (like Shape, Size, Material) that appear between Variant Type
                                    and Colors.
                                    <br><small class="text-muted">These will be shown as dropdowns on the product
                                        page.</small>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="fw-bold">Select Custom Fields:</label>
                                    <select id="customFieldTypeSelector" class="form-control">
                                        <option value="">-- Click to add a field --</option>
                                        @foreach ($customFieldTypes as $fieldType)
                                            <option value="{{ $fieldType->id }}" data-name="{{ $fieldType->name }}">
                                                {{ $fieldType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">
                                        <a href="{{ route('admin.custom-field-type.index') }}" target="_blank">
                                            <i class="ik ik-settings"></i> Manage Custom Field Types
                                        </a>
                                    </small>
                                </div>

                                <div id="selected-custom-fields-list">
                                    <p class="text-muted small">No custom fields selected. Select from dropdown above.</p>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- ================= VARIANTS ================= --}}
                    <div id="variant-section" class="{{ (int) $product->product_type === 3 ? '' : 'd-none' }}">
                        <hr>
                        <h5 class="mb-3">Variants</h5>

                        <div id="variants-wrapper">

                            @php
                                $variantsOld = old('variants');
                                $variantsToShow = $variantsOld ? $variantsOld : $product->variants->toArray();
                            @endphp

                            @foreach ($variantsToShow as $vIndex => $variant)
                                @php
                                    $variantId = is_array($variant) ? $variant['id'] ?? null : $variant->id ?? null;
                                    $variantNameId = is_array($variant)
                                        ? $variant['variant_name_id'] ?? null
                                        : $variant->variant_name_id ?? null;
                                    $variantSku = is_array($variant) ? $variant['sku'] ?? '' : $variant->sku ?? '';
                                    $variantName = is_array($variant) ? $variant['name'] ?? '' : $variant->name ?? '';
                                    $variantMrp = is_array($variant) ? $variant['mrp'] ?? '' : $variant->mrp ?? '';
                                    $variantDescription = is_array($variant)
                                        ? $variant['variant_description'] ?? ''
                                        : $variant->variant_description ?? '';
                                    $variantSell = is_array($variant)
                                        ? $variant['sell_price'] ?? ''
                                        : $variant->sell_price ?? '';
                                    $variantStock = is_array($variant)
                                        ? $variant['stock'] ?? ''
                                        : $variant->stock ?? '';
                                    $variantImage = is_array($variant)
                                        ? $variant['image'] ?? null
                                        : $variant->image ?? null;
                                    $variantCodeTypeId = is_array($variant)
                                        ? $variant['product_code_type_id'] ?? null
                                        : $variant->product_code_type_id ?? null;

                                    $variantAttrs = [];
                                    if ($variantsOld) {
                                        $variantAttrs = $variant['attributes'] ?? [];
                                    } else {
                                        $variantAttrs = $product->variants[$vIndex]->attributes ?? collect([]);
                                    }
                                @endphp

                                <div class="variant-card border rounded p-3 mb-4"
                                    data-variant-index="{{ (int) $vIndex }}">

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">Variant <span
                                                class="variant-index">{{ (int) $vIndex + 1 }}</span>
                                        </h6>
                                        <button type="button"
                                            class="btn btn-sm btn-danger remove-variant {{ (int) $vIndex == 0 ? 'd-none' : '' }}">
                                            Remove Variant
                                        </button>
                                    </div>

                                    @if ($variantId)
                                        <input type="hidden" name="variants[{{ $vIndex }}][id]"
                                            value="{{ $variantId }}">
                                    @endif

                                    {{-- ✅ PRODUCT CODE TYPE - AT VARIANT LEVEL --}}
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="p-3 bg-white border rounded">
                                                <label class="fw-bold">
                                                    <i class="ik ik-tag mr-1"></i> Product Code Type (Optional)
                                                </label>
                                                <select name="variants[{{ $vIndex }}][product_code_type_id]"
                                                    class="form-control product-code-type-select @error("variants.$vIndex.product_code_type_id") is-invalid @enderror">
                                                    <option value="">None - Skip this field</option>
                                                    @foreach ($productCodeTypes as $type)
                                                        <option value="{{ $type->id }}"
                                                            {{ (int) old("variants.$vIndex.product_code_type_id", $variantCodeTypeId) === (int) $type->id ? 'selected' : '' }}>
                                                            {{ $type->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error("variants.$vIndex.product_code_type_id")
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                                <small class="text-muted d-block mt-1">
                                                    <i class="ik ik-info mr-1"></i>
                                                    Optional: Select a code type or leave as "None" to use standard flow
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-md-3">
                                            <label class="fw-bold">Variant Type *</label>
                                            <select class="form-control variant-name-select"
                                                name="variants[{{ $vIndex }}][variant_name_id]">
                                                <option value="">Select</option>
                                                @foreach ($variants as $v)
                                                    <option value="{{ $v->id }}"
                                                        {{ (int) old("variants.$vIndex.variant_name_id", $variantNameId) === (int) $v->id ? 'selected' : '' }}>
                                                        {{ $v->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error("variants.$vIndex.variant_name_id")
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-md-2">
                                            <label class="fw-bold">SKU *</label>
                                            <input type="text" class="form-control"
                                                name="variants[{{ $vIndex }}][sku]"
                                                value="{{ old("variants.$vIndex.sku", $variantSku) }}">
                                            @error("variants.$vIndex.sku")
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        {{-- <div class="col-md-2">
                                            <label class="fw-bold">MRP *</label>
                                            <input type="number" step="0.01" class="form-control"
                                                name="variants[{{ $vIndex }}][mrp]"
                                                value="{{ old("variants.$vIndex.mrp", $variantMrp) }}">
                                            @error("variants.$vIndex.mrp")
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-md-2">
                                            <label class="fw-bold">Sell Price</label>
                                            <input type="number" step="0.01" class="form-control"
                                                name="variants[{{ $vIndex }}][sell_price]"
                                                value="{{ old("variants.$vIndex.sell_price", $variantSell) }}">
                                            @error("variants.$vIndex.sell_price")
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div> --}}

                                        <div class="col-md-2">
                                            <label class="fw-bold">Stock</label>
                                            <input type="number" class="form-control"
                                                name="variants[{{ $vIndex }}][stock]"
                                                value="{{ old("variants.$vIndex.stock", $variantStock) }}">
                                            @error("variants.$vIndex.stock")
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        {{-- </div> --}}

                                        {{-- <div class="row mb-3"> --}}
                                        <div class="col-md-5">
                                            <label class="fw-bold">Variant Image</label>

                                            <input type="file" class="form-control variant-image-input"
                                                name="variants[{{ $vIndex }}][image]" accept="image/*">

                                            @error("variants.$vIndex.image")
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror

                                            <small class="text-muted d-block mt-2 variant-image-note">
                                                Image size must be <strong>600 × 600</strong> pixels (JPG, PNG)
                                            </small>

                                            {{-- Old image preview ONLY when no new file selected --}}
                                            @if (!$variantsOld && $variantImage)
                                                <img src="{{ asset('storage/' . $variantImage) }}" width="70"
                                                    class="mt-2 img-thumbnail variant-old-preview">
                                            @endif
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <label class="fw-bold">Variant Description</label>
                                            <textarea class="form-control" name="variants[{{ $vIndex }}][variant_description]" rows="2">{{ old("variants.$vIndex.variant_description", $variantDescription) }}</textarea>

                                            @error("variants.$vIndex.variant_description")
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                    </div>
                                    {{-- ✅ CUSTOM FIELDS CONTAINER --}}
                                    <div class="custom-variant-fields-container mt-3" style="display:none;"
                                        data-variant-index="{{ $vIndex }}">
                                        {{-- Custom fields will be added here dynamically --}}
                                        @php
                                            // Display existing custom fields for this variant
                                            $existingCustomFields = $variant->customFields ?? [];
                                            // Store existing custom field data in data attributes for JavaScript
                                            if (
                                                $existingCustomFields instanceof
                                                \Illuminate\Database\Eloquent\Collection
                                            ) {
                                                $fieldsData = $existingCustomFields
                                                    ->map(function ($cf) {
                                                        return [
                                                            'id' => $cf->custom_field_type_id,
                                                            'name' => $cf->customFieldType?->name ?? 'Unknown',
                                                            'value' => $cf->field_value,
                                                        ];
                                                    })
                                                    ->toArray();
                                            } else {
                                                $fieldsData = [];
                                            }
                                            $existingCustomFieldsJson = json_encode($fieldsData);
                                        @endphp
                                        <div class="custom-fields-data"
                                            data-fields="{{ htmlspecialchars($existingCustomFieldsJson, ENT_QUOTES) }}"
                                            style="display:none;"></div>
                                    </div>


                                    <div class="border rounded p-3 bg-light">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong>Attributes</strong>
                                            {{-- <button type="button"
                                                class="btn btn-sm btn-outline-primary add-variant-attr">
                                                Add Attribute
                                            </button> --}}
                                        </div>

                                        <div class="variant-attr-wrapper">

                                            @php
                                                $attrList = $variantsOld ? $variantAttrs ?? [] : $variantAttrs;
                                            @endphp

                                            @foreach ($attrList as $aIndex => $attr)
                                                @php
                                                    $attrId = is_array($attr) ? $attr['id'] ?? null : $attr->id ?? null;
                                                    $attributeId = is_array($attr)
                                                        ? $attr['attribute_id'] ?? null
                                                        : $attr->attribute_id ?? null;
                                                    $optionId = is_array($attr)
                                                        ? $attr['attribute_option_id'] ?? null
                                                        : $attr->attribute_option_id ?? null;
                                                    $mrpVal = is_array($attr)
                                                        ? $attr['mrp'] ?? null
                                                        : $attr->mrp ?? null;
                                                    $sellVal = is_array($attr)
                                                        ? $attr['sell_price'] ?? null
                                                        : $attr->sell_price ?? null;
                                                    $imgVal = is_array($attr)
                                                        ? $attr['image'] ?? null
                                                        : $attr->image ?? null;
                                                    $stockVal = is_array($attr)
                                                        ? $attr['stock'] ?? 0
                                                        : $attr->stock ?? 0;
                                                    $minStockVal = is_array($attr)
                                                        ? $attr['min_stock'] ?? 0
                                                        : $attr->min_stock ?? 0;
                                                    // ✅ POR flag
                                                    $isPor = is_array($attr)
                                                        ? isset($attr['is_por']) && (int) $attr['is_por'] === 1
                                                        : isset($attr->is_por) && (int) $attr->is_por === 1;
                                                @endphp

                                                <div class="variant-attr-row row  mb-2">
                                                    @if ($attrId)
                                                        <input type="hidden"
                                                            name="variants[{{ $vIndex }}][attributes][{{ $aIndex }}][id]"
                                                            value="{{ $attrId }}">
                                                    @endif

                                                    <div class="col-md-3">
                                                        <label class="fw-bold">Attribute *</label>
                                                        <select class="form-control variant-attr-select"
                                                            name="variants[{{ $vIndex }}][attributes][{{ $aIndex }}][attribute_id]">
                                                            <option value="">Select Attribute</option>
                                                            @foreach ($attributes as $att)
                                                                <option value="{{ $att->id }}"
                                                                    {{ (int) $att->id === (int) old("variants.$vIndex.attributes.$aIndex.attribute_id", $attributeId) ? 'selected' : '' }}>
                                                                    {{ $att->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error("variants.$vIndex.attributes.$aIndex.attribute_id")
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label class="fw-bold">Option *</label>
                                                        <select class="form-control variant-attr-option"
                                                            data-current="{{ old("variants.$vIndex.attributes.$aIndex.attribute_option_id", $optionId) }}"
                                                            name="variants[{{ $vIndex }}][attributes][{{ $aIndex }}][attribute_option_id]">
                                                            <option value="">Select Option</option>
                                                        </select>
                                                        <input type="hidden" class="variant-attr-option-fallback" name="variants[{{ $vIndex }}][attributes][{{ $aIndex }}][attribute_option_id]" value="{{ old("variants.$vIndex.attributes.$aIndex.attribute_option_id", $optionId) }}">
                                                        @error("variants.$vIndex.attributes.$aIndex.attribute_option_id")
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label class="fw-bold">
                                                            MRP
                                                            <span
                                                                class="text-danger mrp-required-star">{{ $isPor ? '' : '*' }}</span>
                                                            @if ($isPor)
                                                                <span class="badge badge-warning por-badge ml-1">POR</span>
                                                            @endif
                                                        </label>
                                                        <input type="number" step="0.01"
                                                            class="form-control mrp-input {{ $isPor ? 'mrp-por-disabled' : '' }}"
                                                            name="variants[{{ $vIndex }}][attributes][{{ $aIndex }}][mrp]"
                                                            value="{{ old("variants.$vIndex.attributes.$aIndex.mrp", $mrpVal) }}"
                                                            {{ $isPor ? 'disabled' : '' }}>
                                                        {{-- ✅ POR Checkbox --}}
                                                        <div class="form-check mt-1">
                                                            <input type="checkbox" class="form-check-input por-checkbox"
                                                                id="por_{{ $vIndex }}_{{ $aIndex }}"
                                                                name="variants[{{ $vIndex }}][attributes][{{ $aIndex }}][is_por]"
                                                                value="1" {{ $isPor ? 'checked' : '' }}>
                                                            <label class="form-check-label small fw-bold text-warning"
                                                                for="por_{{ $vIndex }}_{{ $aIndex }}">
                                                                Price on Request
                                                            </label>
                                                        </div>
                                                        @error("variants.$vIndex.attributes.$aIndex.mrp")
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label class="fw-bold">Sell Price</label>
                                                        <input type="number" step="0.01"
                                                            class="form-control sell-price-input {{ $isPor ? 'mrp-por-disabled' : '' }}"
                                                            name="variants[{{ $vIndex }}][attributes][{{ $aIndex }}][sell_price]"
                                                            value="{{ old("variants.$vIndex.attributes.$aIndex.sell_price", $sellVal) }}"
                                                            {{ $isPor ? 'disabled' : '' }}>
                                                        @error("variants.$vIndex.attributes.$aIndex.sell_price")
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label class="fw-bold">Stock</label>
                                                        <input type="number" min="0"
                                                            class="form-control attr-stock-input"
                                                            name="variants[{{ $vIndex }}][attributes][{{ $aIndex }}][stock]"
                                                            value="{{ old("variants.$vIndex.attributes.$aIndex.stock", $stockVal) }}"
                                                            placeholder="0">
                                                        <small class="text-muted">Per option</small>
                                                        @error("variants.$vIndex.attributes.$aIndex.stock")
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="fw-bold">Min stock</label>
                                                        <input type="number" min="0" class="form-control"
                                                            name="variants[{{ $vIndex }}][attributes][{{ $aIndex }}][min_stock]"
                                                            value="{{ old("variants.$vIndex.attributes.$aIndex.min_stock", $minStockVal) }}" placeholder="0">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="fw-bold">Image</label>
                                                        <input type="file" class="form-control attr-image-input"
                                                            name="variants[{{ $vIndex }}][attributes][{{ $aIndex }}][image]"
                                                            accept="image/*">

                                                        @error("variants.$vIndex.attributes.$aIndex.image")
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror

                                                        <small class="text-muted d-block mt-2 attr-image-note">
                                                            Image size must be <strong>600 × 600</strong> pixels (JPG, PNG)
                                                        </small>

                                                        {{-- Existing image (EDIT mode) --}}
                                                        @if (!$variantsOld && $imgVal)
                                                            <img src="{{ asset('storage/' . $imgVal) }}"
                                                                class="attr-image-preview mt-1 img-thumbnail"
                                                                width="50">
                                                        @else
                                                            <img class="attr-image-preview mt-1 img-thumbnail d-none"
                                                                width="50">
                                                        @endif
                                                    </div>


                                                    <div class="col-md-12 mt-2">
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger remove-variant-attr {{ $aIndex == 0 ? 'd-none' : '' }}">
                                                            Remove
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach

                                            @if ((is_countable($attrList) ? count($attrList) : $attrList->count()) === 0)
                                                {{-- fallback 1 empty row when no attributes --}}
                                                <div class="variant-attr-row row align-items-end mb-2">
                                                    <div class="col-md-3">
                                                        <label class="fw-bold">Attribute *</label>
                                                        <select class="form-control variant-attr-select"
                                                            name="variants[{{ $vIndex }}][attributes][0][attribute_id]">
                                                            <option value="">Select Attribute</option>
                                                            @foreach ($attributes as $att)
                                                                <option value="{{ $att->id }}">{{ $att->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label class="fw-bold">Option *</label>
                                                        <select class="form-control variant-attr-option" data-current=""
                                                            name="variants[{{ $vIndex }}][attributes][0][attribute_option_id]">
                                                            <option value="">Select Option</option>
                                                        </select>
                                                        <input type="hidden" class="variant-attr-option-fallback" name="variants[{{ $vIndex }}][attributes][0][attribute_option_id]" value="">
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label class="fw-bold">
                                                            MRP <span class="text-danger mrp-required-star">*</span>
                                                        </label>
                                                        <input type="number" step="0.01"
                                                            class="form-control mrp-input"
                                                            name="variants[{{ $vIndex }}][attributes][0][mrp]"
                                                            value="">
                                                        {{-- ✅ POR Checkbox --}}
                                                        <div class="form-check mt-1">
                                                            <input type="checkbox" class="form-check-input por-checkbox"
                                                                id="por_{{ $vIndex }}_0_empty"
                                                                name="variants[{{ $vIndex }}][attributes][0][is_por]"
                                                                value="1">
                                                            <label class="form-check-label small fw-bold text-warning"
                                                                for="por_{{ $vIndex }}_0_empty">
                                                                Price on Request
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label class="fw-bold">Sell Price</label>
                                                        <input type="number" step="0.01"
                                                            class="form-control sell-price-input"
                                                            name="variants[{{ $vIndex }}][attributes][0][sell_price]"
                                                            value="">
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label class="fw-bold">Stock</label>
                                                        <input type="number" min="0" class="form-control attr-stock-input"
                                                            name="variants[{{ $vIndex }}][attributes][0][stock]"
                                                            value="0" placeholder="0">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="fw-bold">Min stock</label>
                                                        <input type="number" min="0" class="form-control"
                                                            name="variants[{{ $vIndex }}][attributes][0][min_stock]"
                                                            value="0" placeholder="0">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="fw-bold">Image</label>
                                                        <input type="file" class="form-control"
                                                            name="variants[{{ $vIndex }}][attributes][0][image]"
                                                            accept="image/*">
                                                    </div>

                                                    <div class="col-md-12 mt-2">
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger remove-variant-attr d-none">
                                                            Remove
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif

                                        </div>

                                         <div class="mt-3 text-right">
                                            <button type="button"  class="btn btn-sm btn-outline-primary add-variant-attr">
                                                Add Attribute
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            @endforeach

                        </div>

                        <button type="button" id="add-variant" class="btn btn-outline-primary">
                            Add Variant
                        </button>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            Update Product
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {

            const optionMap = @json($optionMap ?? []);

            function toggleSectionsByType(val) {
                if (String(val) === '3') {
                    $('#variant-section').removeClass('d-none');
                    $('#simple-section').addClass('d-none');
                } else {
                    $('#simple-section').removeClass('d-none');
                    $('#variant-section').addClass('d-none');
                }
            }

            toggleSectionsByType($('#product_type').val());

            $('#product_type').on('change', function() {
                toggleSectionsByType($(this).val());
            });

            function fillOptions($select, attrId, selected) {
                $select.html('<option value="">Select Option</option>');
                if (!attrId || !optionMap[attrId]) return;

                optionMap[attrId].forEach(function(o) {
                    const sel = String(o.id) === String(selected) ? 'selected' : '';
                    $select.append('<option value="' + o.id + '" ' + sel + '>' + o.name + '</option>');
                });
            }

            function hydrateAllOptions() {
                $('.variant-attr-row').each(function() {
                    const $row = $(this);
                    const attrId = $row.find('.variant-attr-select').val();
                    const selected = $row.find('.variant-attr-option').data('current') || $row.find(
                        '.variant-attr-option').val();
                    fillOptions($row.find('.variant-attr-option'), attrId, selected);
                });
            }

            function reindexAll() {
                $('#variants-wrapper .variant-card').each(function(vIndex) {

                    const $variant = $(this);

                    // ✅ FIX: keep data-variant-index in sync
                    $variant.attr('data-variant-index', vIndex);
                    $variant.find('.variant-index').text(vIndex + 1);

                    $variant.find('[name]').each(function() {
                        const name = $(this).attr('name');
                        if (!name) return;
                        $(this).attr('name', name.replace(/variants\[\d+]/, 'variants[' + vIndex +
                            ']'));
                    });

                    $variant.find('.variant-attr-row').each(function(aIndex) {
                        $(this).find('[name]').each(function() {
                            const name = $(this).attr('name');
                            if (!name) return;
                            $(this).attr('name', name.replace(/\[attributes]\[\d+]/,
                                '[attributes][' + aIndex + ']'));
                        });
                        var optName = 'variants[' + vIndex + '][attributes][' + aIndex + '][attribute_option_id]';
                        $(this).find('.variant-attr-option').attr('name', optName);
                        $(this).find('.variant-attr-option-fallback').attr('name', optName);

                        const $btn = $(this).find('.remove-variant-attr');
                        if ($variant.find('.variant-attr-row').length > 1) {
                            $btn.removeClass('d-none');
                        } else {
                            $btn.addClass('d-none');
                        }
                    });

                    const $removeVariantBtn = $variant.find('.remove-variant');
                    if ($('#variants-wrapper .variant-card').length > 1) {
                        $removeVariantBtn.removeClass('d-none');
                    } else {
                        $removeVariantBtn.addClass('d-none');
                    }
                });

                hydrateAllOptions();
                syncOptionFallbacks();
                // ✅ re-render custom fields after reindexing (also called on add-variant)
                if (typeof updateAllVariantCards === 'function') {
                    updateAllVariantCards();
                }
            }

            $(document).on('change', '.variant-attr-select', function() {
                const $row = $(this).closest('.variant-attr-row');
                fillOptions($row.find('.variant-attr-option'), $(this).val(), null);
                $row.find('.variant-attr-option').data('current', '');
                // Clear validation errors when user selects
                $(this).removeClass('is-invalid').next('.js-attr-error').remove();
            });
            $(document).on('change', '.variant-attr-option', function() {
                $(this).removeClass('is-invalid').next('.js-opt-error').remove();
                var $row = $(this).closest('.variant-attr-row');
                $row.find('.variant-attr-option-fallback').val($(this).val() || '');
            });

            function syncOptionFallbacks() {
                $('.variant-attr-row').each(function() {
                    var optVal = $(this).find('.variant-attr-option').val();
                    $(this).find('.variant-attr-option-fallback').val(optVal || '');
                });
            }

            $('#add-variant').on('click', function() {
                const $first = $('#variants-wrapper .variant-card:first');
                const $clone = $first.clone();

                $clone.find('input[type=text], input[type=number]').val('');
                $clone.find('input[type=file]').val('');
                $clone.find('select').val(''); // ← this already resets code-type select too
                $clone.find('img').remove();

                $clone.find('input[name$="[id]"]').remove();
                // 🔥 IMPORTANT FIX
                $clone.find('.js-image-error').remove();
                $clone.find('.is-invalid').removeClass('is-invalid');

                const $attrWrap = $clone.find('.variant-attr-wrapper');
                const $firstAttr = $attrWrap.find('.variant-attr-row:first').clone();

                $attrWrap.html('');
                $firstAttr.find('input[type=text], input[type=number]').val('');
                $firstAttr.find('input[type=file]').val('');
                $firstAttr.find('select').val('');
                $firstAttr.find('img').remove();
                $firstAttr.find('input[name$="[id]"]').remove();
                $firstAttr.find('.variant-attr-option').data('current', '');
                // 🔥 IMPORTANT FIX
                $firstAttr.find('.js-image-error').remove();
                $firstAttr.find('.is-invalid').removeClass('is-invalid');

                // ✅ Reset POR state on all cloned attribute rows
                $attrWrap.find('.por-checkbox').prop('checked', false);
                $attrWrap.find('.mrp-input').prop('disabled', false).removeClass('mrp-por-disabled');
                $attrWrap.find('.sell-price-input').prop('disabled', false).removeClass('mrp-por-disabled');
                $attrWrap.find('.mrp-required-star').text('*');
                $attrWrap.find('.por-badge').remove();

                $attrWrap.append($firstAttr);

                $('#variants-wrapper').append($clone);
                reindexAll();
                updateVariantNameDropdowns();

            });

            $(document).on('click', '.remove-variant', function() {

                const $card = $(this).closest('.variant-card');

                if ($('#variants-wrapper .variant-card').length <= 1) return;

                Swal.fire({
                    title: 'Remove Variant?',
                    text: 'This variant and its attributes will be removed.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, remove'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $card.remove();
                        reindexAll();
                        updateVariantNameDropdowns();
                    }
                });
            });


            $(document).on('click', '.add-variant-attr', function() {
                const $wrap = $(this).closest('.variant-card').find('.variant-attr-wrapper');
                const $clone = $wrap.find('.variant-attr-row:first').clone();

                $clone.find('input[type=text], input[type=number]').val('');
                $clone.find('input[type=file]').val('');
                $clone.find('select').val('');
                $clone.find('img').remove();

                // 🔥 IMPORTANT FIX
                $clone.find('.js-image-error').remove();
                $clone.find('.is-invalid').removeClass('is-invalid');
                $clone.find('input[name$="[id]"]').remove();
                $clone.find('.variant-attr-option').data('current', '');

                // ✅ Reset POR state on cloned row
                $clone.find('.por-checkbox').prop('checked', false);
                $clone.find('.mrp-input').prop('disabled', false).removeClass('mrp-por-disabled');
                $clone.find('.sell-price-input').prop('disabled', false).removeClass('mrp-por-disabled');
                $clone.find('.mrp-required-star').text('*');
                $clone.find('.por-badge').remove();

                $wrap.append($clone);
                reindexAll();
            });

            $(document).on('click', '.remove-variant-attr', function() {

                const $row = $(this).closest('.variant-attr-row');
                const $wrap = $(this).closest('.variant-attr-wrapper');

                if ($wrap.find('.variant-attr-row').length <= 1) return;

                Swal.fire({
                    title: 'Remove Attribute?',
                    text: 'This attribute will be removed.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, remove'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $row.remove();
                        reindexAll();
                    }
                });
            });

            hydrateAllOptions();
            reindexAll();
        });

        function updateVariantNameDropdowns() {
            // const selected = [];

            // // collect selected values
            // $('.variant-name-select').each(function() {
            //     const val = $(this).val();
            //     if (val) {
            //         selected.push(val);
            //     }
            // });

            // $('.variant-name-select').each(function() {

            //     const currentVal = $(this).val();

            //     $(this).find('option').each(function() {

            //         const optVal = $(this).attr('value');
            //         if (!optVal) return;

            //         // 👇 FIX: current selected option ko kabhi hide/disable mat karo
            //         if (selected.includes(optVal) && optVal !== currentVal) {
            //             $(this).prop('disabled', true).hide();
            //         } else {
            //             $(this).prop('disabled', false).show();
            //         }
            //     });

            //     // 👇 ENSURE selected value stays selected (important for EDIT)
            //     if (currentVal) {
            //         $(this).val(currentVal);
            //     }
            // });
            // ✅ DISABLED - Allow same variant name in multiple variants
            // Customers can now use the same variant name (e.g., "Size") for multiple variant rows

            // ORIGINAL CODE (COMMENTED OUT):
            // This function previously prevented duplicate variant names
            // Now doing nothing - all variant names always available

            return; // Do nothing - allow duplicates
        }
        $(document).on('change', '.variant-name-select', function() {
            updateVariantNameDropdowns();
        });

        $(document).ready(function() {
            updateVariantNameDropdowns();
        });

        $(document).on('input', 'input[name$="[sell_price]"]', function() {

            const $sell = $(this);
            const $row = $sell.closest('.variant-attr-row');

            // ✅ Skip POR rows
            if ($row.find('.por-checkbox').is(':checked')) return;

            const $mrp = $row.find('.mrp-input');

            const sellVal = parseFloat($sell.val());
            const mrpVal = parseFloat($mrp.val());

            // remove old error
            $sell.removeClass('is-invalid');
            $sell.next('.js-sell-error').remove();

            // sell empty → allowed
            if (isNaN(sellVal) || isNaN(mrpVal)) return;

            if (sellVal >= mrpVal) {
                $sell.addClass('is-invalid');
                $sell.after(
                    '<small class="text-danger js-sell-error">Sell price must be less than MRP.</small>'
                );
            }
        });

        function enforceProductCodeTypeRule() {

            let anySelected = false;

            // Check if any variant has product code type selected
            $('.product-code-type-select').each(function() {
                if ($(this).val()) {
                    anySelected = true;
                }
            });

            // If any selected → make all required
            if (anySelected) {
                $('.product-code-type-select').each(function() {

                    const $select = $(this);

                    if (!$select.val()) {

                        $select.addClass('is-invalid');

                        if ($select.next('.js-code-error').length === 0) {
                            $select.after(
                                '<small class="text-danger js-code-error">Product Code Type is required.</small>'
                                );
                        }

                    } else {
                        $select.removeClass('is-invalid');
                        $select.next('.js-code-error').remove();
                    }
                });
            } else {
                // none selected → clear errors
                $('.product-code-type-select').removeClass('is-invalid');
                $('.js-code-error').remove();
            }

            return anySelected;
        }


        /* ================= SIMPLE PRODUCT SELL PRICE ================= */

        function validateSimpleSellPrice() {

            const $price = $('input[name="simple[price]"]');
            const $sell = $('input[name="simple[sale_price]"]');

            // clear old errors
            $price.removeClass('is-invalid');
            $sell.removeClass('is-invalid');
            $sell.next('.js-sell-error').remove();

            const mrpVal = parseFloat($price.val());
            const sellVal = parseFloat($sell.val());

            // MRP required
            if (isNaN(mrpVal)) {
                $price.addClass('is-invalid');
                return false;
            }

            // sell empty → allowed
            if ($sell.val() === '') {
                return true;
            }

            // sell present → must be number
            if (isNaN(sellVal)) {
                $sell.addClass('is-invalid');
                $sell.after('<small class="text-danger js-sell-error">Invalid sell price.</small>');
                return false;
            }

            // sell must be < mrp
            if (sellVal > mrpVal) {
                $sell.addClass('is-invalid');
                $sell.after('<small class="text-danger js-sell-error">Sell price must be less than MRP.</small>');
                return false;
            }

            return true;
        }

        /* LIVE CHECK (optional but good UX) */
        $(document).on(
            'input',
            'input[name="simple[price]"], input[name="simple[sale_price]"]',
            function() {
                validateSimpleSellPrice();
            }
        );
    </script>
    <script>
        /* ================= SELL PRICE VALIDATION ================= */

        function validateAttributeRow($row) {
            const $attrSelect = $row.find('.variant-attr-select');
            const $optSelect = $row.find('.variant-attr-option');

            // Clear previous Attribute / Attribute Value errors
            $attrSelect.removeClass('is-invalid');
            $optSelect.removeClass('is-invalid');
            $attrSelect.siblings('.js-attr-error').remove();
            $optSelect.siblings('.js-opt-error').remove();

            // Attribute and Attribute Value (Option) are required
            let valid = true;
            if (!$attrSelect.val() || $attrSelect.val().trim() === '') {
                $attrSelect.addClass('is-invalid');
                if ($attrSelect.next('.js-attr-error').length === 0) {
                    $attrSelect.after('<small class="text-danger js-attr-error">Attribute is required.</small>');
                }
                valid = false;
            }
            if (!$optSelect.val() || $optSelect.val().trim() === '') {
                $optSelect.addClass('is-invalid');
                if ($optSelect.next('.js-opt-error').length === 0) {
                    $optSelect.after('<small class="text-danger js-opt-error">Attribute Value is required.</small>');
                }
                valid = false;
            }
            if (!valid) return false;

            // ✅ Skip POR rows — no price required
            const $porCheckbox = $row.find('.por-checkbox');
            if ($porCheckbox.length && $porCheckbox.is(':checked')) {
                return true;
            }

            const $mrp = $row.find('.mrp-input');
            const $sell = $row.find('.sell-price-input');

            const mrpVal = parseFloat($mrp.val());
            const sellVal = parseFloat($sell.val());

            // clear old error
            $sell.removeClass('is-invalid');
            $sell.next('.js-sell-error').remove();

            // empty values allowed
            if (isNaN(mrpVal) || isNaN(sellVal)) {
                return true;
            }

            if (sellVal >= mrpVal) {
                $sell.addClass('is-invalid');
                $sell.after(
                    '<small class="text-danger js-sell-error">Sell price must be less than MRP.</small>'
                );
                return false;
            }

            return true;
        }

        /* ================= LIVE VALIDATION ================= */

        $(document).on('input',
            'input[name$="[sell_price]"], input[name$="[mrp]"]',
            function() {

                const $row = $(this).closest('.variant-attr-row');
                validateAttributeRow($row);
            }
        );
        $(document).on('change', '.product-code-type-select', function() {
            enforceProductCodeTypeRule();
        });


        /* ================= FORM SUBMIT BLOCK ================= */

        $(document).on('submit', '#product-form', function(e) {

            let hasError = false;
            const productType = $('#product_type').val();

            // Product Code Type Rule (variant only)
            if (productType === '3' && enforceProductCodeTypeRule()) {
                $('.product-code-type-select').each(function() {
                    if (!$(this).val()) {
                        hasError = true;
                    }
                });
            }

            // Attribute & Attribute Value validation (variant only)
            if (productType === '3') {
                $('.variant-attr-row').each(function() {
                    if (!validateAttributeRow($(this))) {
                        hasError = true;
                    }
                });
            }

            if (hasError) {
                e.preventDefault();

                // scroll to first error
                const $firstError = $('.is-invalid:first');
                if ($firstError.length) {
                    $('html, body').animate({
                        scrollTop: $firstError.offset().top - 120
                    }, 400);
                }

                return false;
            }
        });
    </script>

    <script>
        function validateImage600(fileInput, previewSelector) {

            const file = fileInput.files[0];
            const $input = $(fileInput);

            // remove old error always
            $input.removeClass('is-invalid');
            $input.siblings('.js-image-error').remove();

            if (!file) {
                return true; // ❗ no file = no error
            }

            const img = new Image();
            const reader = new FileReader();

            reader.onload = function(e) {
                img.src = e.target.result;
            };

            img.onload = function() {

                if (img.width !== 600 || img.height !== 600) {

                    $input.addClass('is-invalid');
                    $input.after(
                        '<small class="text-danger js-image-error">Image must be exactly 600 × 600.</small>'
                    );

                    // hide preview (only if exists)
                    if (previewSelector && previewSelector.length) {
                        previewSelector.addClass('d-none');
                    }

                    // reset file
                    fileInput.value = '';
                }
            };

            reader.readAsDataURL(file);
            return true;
        }

        /* MAIN IMAGE */
        $(document).on('change', 'input[name="images"]', function() {
            const $preview = $(this).closest('.col-md-6').find('img');
            validateImage600(this, $preview);
        });


        /* VARIANT IMAGE */
        $(document).on('change', '.variant-image-input', function() {
            const $card = $(this).closest('.variant-card');
            const $preview = $card.find('.variant-old-preview');
            validateImage600(this, $preview);
        });

        /* ATTRIBUTE IMAGE */
        $(document).on('change', '.attr-image-input', function() {
            const $row = $(this).closest('.variant-attr-row');
            const $preview = $row.find('.attr-image-preview');
            validateImage600(this, $preview);
        });

        /* ================================================================
           ✅ DYNAMIC: CUSTOM FIELD TYPES MANAGEMENT (EDIT MODE)
           ================================================================ */

        @php
            // Build existing custom fields map: variant_id => [{type_id, type_name, value}]
            $variantCustomFieldsMapData = $product->variants->mapWithKeys(function ($v) {
                return [
                    $v->id => $v->customFields
                        ->map(function ($cf) {
                            return [
                                'type_id' => $cf->custom_field_type_id,
                                'type_name' => $cf->customFieldType ? $cf->customFieldType->name : '',
                                'value' => $cf->field_value,
                            ];
                        })
                        ->values(),
                ];
            });
        @endphp

        // Build existing custom fields from PHP — keyed by variant DB id
        const variantCustomFieldsMap = @json($variantCustomFieldsMapData);

        let selectedCustomFieldTypes = []; // Array of {id, name}

        // ---------------------------------------------------------------
        // Initialise on page load
        // ---------------------------------------------------------------
        $(document).ready(function() {

            // 1. Collect unique field types from ALL variants
            Object.values(variantCustomFieldsMap).forEach(fields => {
                fields.forEach(field => {
                    if (!selectedCustomFieldTypes.some(f => f.id == field.type_id)) {
                        selectedCustomFieldTypes.push({
                            id: field.type_id,
                            name: field.type_name
                        });
                    }
                });
            });

            if (selectedCustomFieldTypes.length > 0) {
                updateSelectedFieldsList();
                renderCustomFieldsForAllVariants();
            }
        });

        // ---------------------------------------------------------------
        // Show / hide section when product type changes (disabled on edit
        // because the select is disabled, but kept for safety)
        // ---------------------------------------------------------------
        $('#product_type').on('change', function() {
            if ($(this).val() === '3') {
                $('#custom-fields-config-section').removeClass('d-none');
            } else {
                $('#custom-fields-config-section').addClass('d-none');
                selectedCustomFieldTypes = [];
                $('#selected-custom-fields-list').html(
                '<p class="text-muted small">No custom fields selected.</p>');
                updateAllVariantCards();
            }
        });

        // ---------------------------------------------------------------
        // Dropdown: add a new field type
        // ---------------------------------------------------------------
        $('#customFieldTypeSelector').on('change', function() {
            const fieldTypeId = $(this).val();
            const fieldTypeName = $(this).find('option:selected').data('name');
            if (!fieldTypeId) return;

            if (selectedCustomFieldTypes.some(f => f.id == fieldTypeId)) {
                alert('This field is already added');
                $(this).val('');
                return;
            }

            selectedCustomFieldTypes.push({
                id: fieldTypeId,
                name: fieldTypeName
            });
            $(this).val('');
            updateSelectedFieldsList();
            updateAllVariantCards();
        });

        // ---------------------------------------------------------------
        // Remove a field type
        // ---------------------------------------------------------------
        $(document).on('click', '.remove-custom-field-type', function() {
            const fieldId = $(this).data('field-id');
            selectedCustomFieldTypes = selectedCustomFieldTypes.filter(f => f.id != fieldId);
            updateSelectedFieldsList();
            updateAllVariantCards();
        });

        // ---------------------------------------------------------------
        // Render the "selected" chips list
        // ---------------------------------------------------------------
        function updateSelectedFieldsList() {
            if (selectedCustomFieldTypes.length === 0) {
                $('#selected-custom-fields-list').html('<p class="text-muted small">No custom fields selected.</p>');
                return;
            }
            let html = '';
            selectedCustomFieldTypes.forEach(field => {
                html += `
                <div class="alert alert-success d-flex justify-content-between align-items-center mb-2 p-2">
                    <div><i class="ik ik-check-circle mr-2"></i><strong>${field.name}</strong></div>
                    <button type="button" class="btn btn-sm btn-danger remove-custom-field-type"
                            data-field-id="${field.id}">
                        <i class="ik ik-trash-2"></i>
                    </button>
                </div>`;
            });
            $('#selected-custom-fields-list').html(html);
        }

        // ---------------------------------------------------------------
        // Render custom fields for ALL variant cards using DB values
        // (called once on page load)
        // ---------------------------------------------------------------
        function renderCustomFieldsForAllVariants() {
            $('.variant-card').each(function() {
                const $card = $(this);
                const vIndex = $card.data('variant-index');
                const variantId = $card.find('input[name$="[id]"]').val();
                const dbFields = variantCustomFieldsMap[variantId] || [];

                renderCustomFieldsForCard($card, vIndex, dbFields);
            });
        }

        // ---------------------------------------------------------------
        // Render / re-render custom fields for ONE card
        // Preserves any values already typed, then fills from DB
        // ---------------------------------------------------------------
        function renderCustomFieldsForCard($card, vIndex, dbFields) {
            const $container = $card.find('.custom-variant-fields-container');

            if (selectedCustomFieldTypes.length === 0) {
                $container.hide().html('');
                return;
            }

            // Save values currently in the DOM (user may have typed something)
            const savedValues = {};
            $container.find('input[type="text"]').each(function() {
                const n = $(this).attr('name'),
                    v = $(this).val();
                if (n) savedValues[n] = v;
            });

            let html = '<div class="alert alert-primary mb-2 p-2"><strong>' +
                '<i class="ik ik-layers mr-1"></i> Additional Selection Fields</strong></div>';

            selectedCustomFieldTypes.forEach(field => {
                const fieldName = `variants[${vIndex}][custom_fields][${field.id}]`;

                // Priority: 1) user typed, 2) DB value, 3) empty
                let value = savedValues[fieldName] || '';
                if (!value) {
                    const dbField = dbFields.find(f => f.type_id == field.id);
                    if (dbField) value = dbField.value;
                }

                html += `
                <div class="row mb-2">
                    <div class="col-md-12">
                        <label class="fw-bold">${field.name}</label>
                        <input type="text"
                               name="${fieldName}"
                               class="form-control custom-field-value"
                               placeholder="Enter ${field.name.toLowerCase()}"
                               value="${value}">
                    </div>
                </div>`;
            });

            $container.html(html).show();
        }

        // ---------------------------------------------------------------
        // Re-render ALL cards (used when adding / removing field types
        // or when a new variant card is cloned)
        // ---------------------------------------------------------------
        function updateAllVariantCards() {
            $('.variant-card').each(function() {
                const $card = $(this);
                const vIndex = $card.data('variant-index');
                const variantId = $card.find('input[name$="[id]"]').val();
                const dbFields = variantCustomFieldsMap[variantId] || [];
                renderCustomFieldsForCard($card, vIndex, dbFields);
            });
        }

        /* ================================================================
           ✅ POR (Price on Request) CHECKBOX LOGIC
           ================================================================ */

        // Helper: apply POR disabled/enabled state to a row
        function applyPorState($row, isPor) {
            const $mrpInput = $row.find('.mrp-input');
            const $sellInput = $row.find('.sell-price-input');
            const $star = $row.find('.mrp-required-star');
            const $label = $row.find('label').first();

            // Remove existing POR badge
            $row.find('.por-badge').remove();

            if (isPor) {
                $mrpInput.val('').prop('disabled', true).addClass('mrp-por-disabled');
                $sellInput.val('').prop('disabled', true).addClass('mrp-por-disabled');
                $mrpInput.removeClass('is-invalid');
                $sellInput.removeClass('is-invalid');
                $sellInput.next('.js-sell-error').remove();
                $star.text('');
                $label.append('<span class="badge badge-warning por-badge ml-1">POR</span>');
            } else {
                $mrpInput.prop('disabled', false).removeClass('mrp-por-disabled');
                $sellInput.prop('disabled', false).removeClass('mrp-por-disabled');
                $star.text('*');
            }
        }

        // Live: POR checkbox change
        $(document).on('change', '.por-checkbox', function() {
            const $row = $(this).closest('.variant-attr-row');
            applyPorState($row, $(this).is(':checked'));
        });

        // Init: apply POR state for already-checked checkboxes on page load
        $(document).ready(function() {
            $('.por-checkbox:checked').each(function() {
                applyPorState($(this).closest('.variant-attr-row'), true);
            });
        });
    </script>
@endpush
