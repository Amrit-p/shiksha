@extends('admin.layouts.main')
@section('title', 'Add Product')

@section('content')
    <div class="container-fluid">

        <div class="page-header mb-4">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title d-flex align-items-center gap-3">
                        <i class="ik ik-box bg-primary text-white p-3 rounded-circle"></i>
                        <div>
                            <h4 class="mb-0">Add Product</h4>
                            <small class="text-muted">Supports Simple + Variant Products</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data"
                    id="product-form">
                    @csrf

                    <h5 class="text-primary mb-3">Product Information</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Product Title <span class="text-danger">*</span></label>
                            <input name="title" type="text"
                                class="form-control shadow-sm @error('title') is-invalid @enderror"
                                value="{{ old('title') }}">
                            @error('title')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold">Category <span class="text-danger">*</span></label>
                            <select name="category_id"
                                class="form-control shadow-sm @error('category_id') is-invalid @enderror">
                                <option value="">Select</option>
                                @foreach ($categories as $item)
                                    <option value="{{ $item->id }}"
                                        {{ (string) old('category_id') === (string) $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
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
                            <label class="fw-bold">Product Mode <span class="text-danger">*</span></label>
                            <select name="product_type" id="product_type"
                                class="form-control shadow-sm @error('product_type') is-invalid @enderror">
                                <option value="">Select</option>
                                <option value="1" {{ (string) old('product_type') === '1' ? 'selected' : '' }}>Simple
                                </option>
                                <option value="3" {{ (string) old('product_type') === '3' ? 'selected' : '' }}>Variant
                                </option>
                            </select>
                            @error('product_type')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold">Short Description <span class="text-danger">*</span></label>
                            <input name="short_description" type="text" maxlength="150"
                                class="form-control shadow-sm @error('short_description') is-invalid @enderror"
                                value="{{ old('short_description') }}">
                            <small class="text-muted d-block mt-1"><span id="char-count">0</span>/150</small>
                            @error('short_description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold">Description</label>
                        <textarea name="description" class="form-control shadow-sm" rows="4">{{ old('description') }}</textarea>
                        @error('description')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold">Main Image <span class="text-danger">*</span></label>
                            <input type="file" name="images" id="main_image"
                                class="form-control shadow-sm @error('images') is-invalid @enderror"
                                accept="image/png,image/jpeg,image/jpg">
                            <small class="text-muted d-block mt-1 main-image-note">
                                Image size must be <strong>600 × 600</strong> pixels (JPG, PNG)
                            </small>
                            @error('images')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold ">Unit *</label>
                            <select name="unit_id" class="form-control shadow-sm @error('unit_id') is-invalid @enderror">
                                <option value="">Select</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}"
                                        {{ (string) old('unit_id') === (string) $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unit_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    {{-- SIMPLE PRODUCT SECTION --}}
                    <div id="simple-section" class="{{ (string) old('product_type') === '1' ? '' : 'd-none' }}">
                        <hr class="my-4">
                        <h5 class="text-primary mb-3">Simple Product</h5>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="fw-bold">MRP *</label>
                                <input type="number" step="0.01" name="simple[price]"
                                    class="form-control shadow-sm @error('simple.price') is-invalid @enderror"
                                    value="{{ old('simple.price') }}">
                                @error('simple.price')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Sell Price </label>
                                <input type="number" step="0.01" name="simple[sale_price]"
                                    class="form-control shadow-sm @error('simple.sale_price') is-invalid @enderror"
                                    value="{{ old('simple.sale_price') }}">
                                @error('simple.sale_price')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Stock</label>
                                <input type="number" name="simple[stock]"
                                    class="form-control shadow-sm @error('simple.stock') is-invalid @enderror"
                                    value="{{ old('simple.stock') }}">
                                @error('simple.stock')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Min stock (alert when below)</label>
                                <input type="number" min="0" name="simple[min_stock]"
                                    class="form-control shadow-sm"
                                    value="{{ old('simple.min_stock', 0) }}" placeholder="0">
                                <small class="text-muted">Dashboard shows restock list when stock &lt; this</small>
                            </div>
                        </div>
                    </div>

                    {{-- ✅ CUSTOM FIELD TYPES SELECTION --}}
                    <div id="custom-fields-config-section" class="d-none">
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


                    {{-- VARIANT PRODUCT SECTION --}}
                    <div id="variant-section" class="{{ (string) old('product_type') === '3' ? '' : 'd-none' }}">
                        <hr class="my-4">
                        <h5 class="text-primary mb-3">Variants</h5>

                        <div id="variants-wrapper">
                            @php
                                $oldVariants = old('variants');
                                if (!is_array($oldVariants) || count($oldVariants) < 1) {
                                    $oldVariants = [
                                        [
                                            'variant_name_id' => '',
                                            'product_code_type_id' => '',
                                            'sku' => '',
                                            'stock' => '',
                                            'attributes' => [
                                                [
                                                    'attribute_id' => '',
                                                    'attribute_option_id' => '',
                                                    'mrp' => '',
                                                    'sell_price' => '',
                                                ],
                                            ],
                                        ],
                                    ];
                                }
                            @endphp

                            @foreach ($oldVariants as $vIndex => $v)
                                @php
                                    $oldAttrs = $v['attributes'] ?? [];
                                    if (!is_array($oldAttrs) || count($oldAttrs) < 1) {
                                        $oldAttrs = [
                                            [
                                                'attribute_id' => '',
                                                'attribute_option_id' => '',
                                                'mrp' => '',
                                                'sell_price' => '',
                                            ],
                                        ];
                                    }
                                @endphp

                                <div class="variant-card border rounded p-3 mb-4 shadow-sm bg-light"
                                    data-variant-index="{{ $vIndex }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 text-dark">
                                            <span class="variant-title">Variant {{ $vIndex + 1 }}</span>
                                        </h6>
                                        <button type="button"
                                            class="btn btn-sm btn-danger remove-variant {{ $vIndex === 0 ? 'd-none' : '' }}">
                                            Remove
                                        </button>
                                    </div>

                                    {{-- Product Code Type (Optional) --}}
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="p-3 bg-white border rounded">
                                                <label class="fw-bold">
                                                    <i class="ik ik-tag mr-1"></i> Product Code Type (Optional)
                                                </label>
                                                <select name="variants[{{ $vIndex }}][product_code_type_id]"
                                                    class="form-control shadow-sm  product-code-type-select @error("variants.$vIndex.product_code_type_id") is-invalid @enderror">
                                                    <option value="">None - Skip this field</option>
                                                    @foreach ($productCodeTypes as $type)
                                                        <option value="{{ $type->id }}"
                                                            {{ (string) old("variants.$vIndex.product_code_type_id", $v['product_code_type_id'] ?? '') === (string) $type->id ? 'selected' : '' }}>
                                                            {{ $type->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error("variants.$vIndex.product_code_type_id")
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-md-3">
                                            <label class="fw-bold">Variant Type *</label>
                                            <select name="variants[{{ $vIndex }}][variant_name_id]"
                                                class="form-control shadow-sm variant-name-select @error("variants.$vIndex.variant_name_id") is-invalid @enderror">
                                                <option value="">Select</option>
                                                @foreach ($variants as $variant)
                                                    <option value="{{ $variant->id }}"
                                                        {{ (string) old("variants.$vIndex.variant_name_id", $v['variant_name_id'] ?? '') === (string) $variant->id ? 'selected' : '' }}>
                                                        {{ $variant->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error("variants.$vIndex.variant_name_id")
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <label class="fw-bold">SKU *</label>
                                            <input type="text"
                                                class="form-control shadow-sm @error("variants.$vIndex.sku") is-invalid @enderror"
                                                name="variants[{{ $vIndex }}][sku]"
                                                value="{{ old("variants.$vIndex.sku", $v['sku'] ?? '') }}">
                                            @error("variants.$vIndex.sku")
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <label class="fw-bold">Stock *</label>
                                            <input type="number"
                                                class="form-control shadow-sm @error("variants.$vIndex.stock") is-invalid @enderror"
                                                name="variants[{{ $vIndex }}][stock]"
                                                value="{{ old("variants.$vIndex.stock", $v['stock'] ?? '') }}">
                                            @error("variants.$vIndex.stock")
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <label class="fw-bold">Image *</label>
                                            <input type="file"
                                                class="form-control shadow-sm @error("variants.$vIndex.image") is-invalid @enderror"
                                                name="variants[{{ $vIndex }}][image]" accept="image/*">
                                            @error("variants.$vIndex.image")
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted d-block mt-2 variant-image-note">
                                                600 × 600 pixels
                                            </small>
                                        </div>

                                        <div class="col-md-12">
                                            <label class="fw-bold">Variant Description</label>
                                            <textarea
                                                class="form-control shadow-sm @error("variants.$vIndex.variant_description") is-invalid @enderror"
                                                name="variants[{{ $vIndex }}][variant_description]"
                                                rows="2">{{ old("variants.$vIndex.variant_description", $v['variant_description'] ?? '') }}</textarea>

                                            @error("variants.$vIndex.variant_description")
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- ✅ CUSTOM FIELDS CONTAINER --}}
                                    <div class="custom-variant-fields-container mt-3" style="display:none;">
                                        {{-- Custom fields will be added here dynamically --}}
                                    </div>
                                    {{-- ATTRIBUTES SECTION --}}
                                    <div class="border rounded p-3 bg-white mt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0">Attribute Options</h6>
                                            {{-- <button type="button"
                                                class="btn btn-sm btn-outline-primary add-variant-attr">
                                                Add Attribute
                                            </button> --}}
                                        </div>

                                        <div class="variant-attr-wrapper">
                                            @foreach ($oldAttrs as $aIndex => $a)
                                                <div class="variant-attr-row row g-2 mb-2"
                                                    data-attr-index="{{ $aIndex }}">
                                                    <div class="col-md-2">
                                                        <label class="fw-bold">Attribute *</label>
                                                        <select
                                                            name="variants[{{ $vIndex }}][attributes][{{ $aIndex }}][attribute_id]"
                                                            class="form-control shadow-sm variant-attr-select @error("variants.$vIndex.attributes.$aIndex.attribute_id") is-invalid @enderror">
                                                            <option value="">Select</option>
                                                            @foreach ($attributes as $attr)
                                                                <option value="{{ $attr->id }}"
                                                                    {{ (string) old("variants.$vIndex.attributes.$aIndex.attribute_id", $a['attribute_id'] ?? '') === (string) $attr->id ? 'selected' : '' }}>
                                                                    {{ $attr->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error("variants.$vIndex.attributes.$aIndex.attribute_id")
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="fw-bold">Option *</label>
                                                        <select
                                                            name="variants[{{ $vIndex }}][attributes][{{ $aIndex }}][attribute_option_id]"
                                                            class="form-control shadow-sm variant-attr-option @error("variants.$vIndex.attributes.$aIndex.attribute_option_id") is-invalid @enderror">
                                                            <option value="">Select Option</option>
                                                        </select>
                                                        @error("variants.$vIndex.attributes.$aIndex.attribute_option_id")
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label class="fw-bold">
                                                            MRP <span class="mrp-required-star text-danger">*</span>
                                                        </label>
                                                        <input type="number" step="0.01"
                                                            name="variants[{{ $vIndex }}][attributes][{{ $aIndex }}][mrp]"
                                                            class="form-control shadow-sm mrp-input @error("variants.$vIndex.attributes.$aIndex.mrp") is-invalid @enderror"
                                                            value="{{ old("variants.$vIndex.attributes.$aIndex.mrp", $a['mrp'] ?? '') }}">

                                                        {{-- ✅ NEW: POR Checkbox --}}
                                                        <div class="form-check mt-2">
                                                            <input type="checkbox" class="form-check-input por-checkbox"
                                                                name="variants[{{ $vIndex }}][attributes][{{ $aIndex }}][is_por]"
                                                                id="por_{{ $vIndex }}_{{ $aIndex }}"
                                                                value="1"
                                                                {{ old("variants.$vIndex.attributes.$aIndex.is_por", $a['is_por'] ?? 0) ? 'checked' : '' }}>
                                                            <label class="form-check-label small"
                                                                for="por_{{ $vIndex }}_{{ $aIndex }}">
                                                                <strong>POR</strong> (Price on Request)
                                                            </label>
                                                        </div>

                                                        @error("variants.$vIndex.attributes.$aIndex.mrp")
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="fw-bold">Sell Price</label>
                                                        <input type="number" step="0.01"
                                                            name="variants[{{ $vIndex }}][attributes][{{ $aIndex }}][sell_price]"
                                                            class="form-control shadow-sm sell-price-input"
                                                            value="{{ old("variants.$vIndex.attributes.$aIndex.sell_price", $a['sell_price'] ?? '') }}">
                                                        @error("variants.$vIndex.attributes.$aIndex.sell_price")
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label class="fw-bold">Stock</label>
                                                        <input type="number" min="0"
                                                            name="variants[{{ $vIndex }}][attributes][{{ $aIndex }}][stock]"
                                                            class="form-control shadow-sm"
                                                            value="{{ old("variants.$vIndex.attributes.$aIndex.stock", $a['stock'] ?? 0) }}"
                                                            placeholder="0">
                                                        <small class="text-muted">Per option (e.g. per color)</small>
                                                        @error("variants.$vIndex.attributes.$aIndex.stock")
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="fw-bold">Min stock</label>
                                                        <input type="number" min="0"
                                                            name="variants[{{ $vIndex }}][attributes][{{ $aIndex }}][min_stock]"
                                                            class="form-control shadow-sm"
                                                            value="{{ old("variants.$vIndex.attributes.$aIndex.min_stock", $a['min_stock'] ?? 0) }}"
                                                            placeholder="0">
                                                        <small class="text-muted">Alert when below</small>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="fw-bold">Image *</label>
                                                        <input type="file"
                                                            name="variants[{{ $vIndex }}][attributes][{{ $aIndex }}][image]"
                                                            class="form-control shadow-sm @error("variants.$vIndex.attributes.$aIndex.image") is-invalid @enderror"
                                                            accept="image/*">
                                                        @error("variants.$vIndex.attributes.$aIndex.image")
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                        <small class="text-muted d-block mt-2">600 × 600 px</small>
                                                    </div>

                                                    <div class="col-12 mt-2">
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger remove-variant-attr {{ $aIndex === 0 ? 'd-none' : '' }}">
                                                            Remove Color
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div> 
                                        <!-- 👇 Button Niche Shift Kiya -->
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
                            <i class="ik ik-plus mr-1"></i> Add Variant
                        </button>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="ik ik-save mr-2"></i> Save Product
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
@endsection

@push('script')
    <script>
        const optionMap = @json($optionMap ?? []);

        $(document).ready(function() {





            /* ================================================================
               ✅ POR (Price on Request) CHECKBOX LOGIC
               ================================================================ */

            // Handle POR checkbox change
            $(document).on('change', '.por-checkbox', function() {
                const $row = $(this).closest('.variant-attr-row');
                const $mrpInput = $row.find('.mrp-input');
                const $sellPriceInput = $row.find('.sell-price-input');
                const $mrpStar = $row.find('.mrp-required-star');

                if ($(this).is(':checked')) {
                    // POR is checked - disable price fields
                    $mrpInput.val('').prop('required', false).prop('disabled', true)
                        .css('background-color', '#e9ecef');
                    $sellPriceInput.val('').prop('disabled', true)
                        .css('background-color', '#e9ecef');
                    $mrpStar.hide();
                } else {
                    // POR is unchecked - enable price fields
                    $mrpInput.prop('required', true).prop('disabled', false)
                        .css('background-color', '');
                    $sellPriceInput.prop('disabled', false)
                        .css('background-color', '');
                    $mrpStar.show();
                }
            });

            // Initialize POR checkboxes on page load
            $('.por-checkbox').each(function() {
                if ($(this).is(':checked')) {
                    const $row = $(this).closest('.variant-attr-row');
                    const $mrpInput = $row.find('.mrp-input');
                    const $sellPriceInput = $row.find('.sell-price-input');
                    const $mrpStar = $row.find('.mrp-required-star');

                    $mrpInput.val('').prop('required', false).prop('disabled', true)
                        .css('background-color', '#e9ecef');
                    $sellPriceInput.val('').prop('disabled', true)
                        .css('background-color', '#e9ecef');
                    $mrpStar.hide();
                }
            });

            /* ================================================================
               ✅ DYNAMIC: CUSTOM FIELD TYPES MANAGEMENT
               ================================================================ */

            let selectedCustomFieldTypes = []; // Array of {id, name}

            // Show/hide custom fields config when variant product selected
            $('#product_type').on('change', function() {
                if ($(this).val() === '3') {
                    $('#variant-section').removeClass('d-none');
                    $('#custom-fields-config-section').removeClass('d-none');
                } else {
                    $('#variant-section').addClass('d-none');
                    $('#custom-fields-config-section').addClass('d-none');
                    selectedCustomFieldTypes = [];
                    $('#selected-custom-fields-list').html(
                        '<p class="text-muted small">No custom fields selected.</p>');
                    updateAllVariantCards();
                }
            });

            // Add custom field type when selected from dropdown
            $('#customFieldTypeSelector').on('change', function() {
                const fieldTypeId = $(this).val();
                const fieldTypeName = $(this).find('option:selected').data('name');

                if (!fieldTypeId) return;

                // Check if already added
                if (selectedCustomFieldTypes.some(f => f.id == fieldTypeId)) {
                    alert('This field is already added');
                    $(this).val('');
                    return;
                }

                // Add to selected list
                selectedCustomFieldTypes.push({
                    id: fieldTypeId,
                    name: fieldTypeName
                });

                // Reset selector
                $(this).val('');

                // Update UI
                updateSelectedFieldsList();
                updateAllVariantCards();
            });

            // Update the list of selected custom fields
            function updateSelectedFieldsList() {
                let html = '';

                if (selectedCustomFieldTypes.length === 0) {
                    html = '<p class="text-muted small">No custom fields selected. Select from dropdown above.</p>';
                } else {
                    selectedCustomFieldTypes.forEach((field, index) => {
                        html += `
                            <div class="alert alert-success d-flex justify-content-between align-items-center mb-2 p-2">
                                <div>
                                    <i class="ik ik-check-circle mr-2"></i>
                                    <strong>${field.name}</strong>
                                </div>
                                <button type="button"
                                        class="btn btn-sm btn-danger remove-custom-field-type"
                                        data-field-id="${field.id}">
                                    <i class="ik ik-trash-2"></i>
                                </button>
                            </div>
                        `;
                    });
                }

                $('#selected-custom-fields-list').html(html);
            }

            // Remove custom field type
            $(document).on('click', '.remove-custom-field-type', function() {
                const fieldId = $(this).data('field-id');
                selectedCustomFieldTypes = selectedCustomFieldTypes.filter(f => f.id != fieldId);
                updateSelectedFieldsList();
                updateAllVariantCards();
            });

            // ✅ UPDATED: Update all variant cards with custom fields (preserves existing values)
            function updateAllVariantCards() {
                $('.variant-card').each(function() {
                    const vIndex = $(this).data('variant-index');
                    const $container = $(this).find('.custom-variant-fields-container');

                    if (selectedCustomFieldTypes.length === 0) {
                        $container.hide().html('');
                        return;
                    }

                    // ✅ SAVE EXISTING VALUES BEFORE REGENERATING
                    const existingValues = {};
                    $container.find('input[type="text"]').each(function() {
                        const name = $(this).attr('name');
                        const value = $(this).val();
                        if (name && value) {
                            existingValues[name] = value;
                        }
                    });

                    let fieldsHtml =
                        '<div class="alert alert-primary mb-2 p-2"><strong><i class="ik ik-layers mr-1"></i> Additional Selection Fields</strong></div>';

                    selectedCustomFieldTypes.forEach(field => {
                        const fieldName = `variants[${vIndex}][custom_fields][${field.id}]`;
                        const savedValue = existingValues[fieldName] || '';

                        fieldsHtml += `
                            <div class="row mb-2" data-field-id="${field.id}">
                                <div class="col-md-12">
                                    <label class="fw-bold">${field.name}</label>
                                    <input type="text"
                                           name="${fieldName}"
                                           class="form-control custom-field-value"
                                           placeholder="Enter ${field.name.toLowerCase()}"
                                           value="${savedValue}">
                                </div>
                            </div>
                        `;
                    });

                    $container.html(fieldsHtml).show();
                });
            }

            /* ================================================================
               CHARACTER COUNT
               ================================================================ */
            $('input[name="short_description"]').on('input', function() {
                $('#char-count').text($(this).val().length);
            });

            /* ================================================================
               PRODUCT TYPE TOGGLE
               ================================================================ */
            function showSection(type) {
                $('#simple-section, #variant-section').addClass('d-none');
                if (type === 'simple') {
                    $('#simple-section').removeClass('d-none');
                    toggleSectionFields('simple');
                } else if (type === 'variant') {
                    $('#variant-section').removeClass('d-none');
                    toggleSectionFields('variant');
                } else {
                    toggleSectionFields('none');
                }
            }

            function toggleSectionFields(type) {
                if (type === 'simple') {
                    $('#simple-section input, #simple-section select').prop('disabled', false);
                    $('#variant-section input, #variant-section select, #variant-section textarea').prop('disabled',
                        true);
                } else if (type === 'variant') {
                    $('#simple-section input, #simple-section select').prop('disabled', true);
                    $('#variant-section input, #variant-section select, #variant-section textarea').prop('disabled',
                        false);
                } else {
                    $('#simple-section input, #simple-section select').prop('disabled', true);
                    $('#variant-section input, #variant-section select, #variant-section textarea').prop('disabled',
                        true);
                }
            }

            $('#product_type').on('change', function() {
                const val = $(this).val();
                if (val === '1') {
                    showSection('simple');
                } else if (val === '3') {
                    showSection('variant');
                } else {
                    showSection('none');
                }
            });

            /* ================================================================
               VARIANT MANAGEMENT
               ================================================================ */
            function reindexVariants() {
                $('.variant-card').each(function(idx) {
                    $(this).attr('data-variant-index', idx);
                    $(this).find('.variant-title').text('Variant ' + (idx + 1));

                    $(this).find('[name^="variants["]').each(function() {
                        const oldName = $(this).attr('name');
                        const newName = oldName.replace(/variants\[\d+\]/, 'variants[' + idx + ']');
                        $(this).attr('name', newName);
                    });

                    $(this).find('.variant-attr-row').each(function(aIdx) {
                        $(this).attr('data-attr-index', aIdx);
                        $(this).find('[name*="[attributes]["]').each(function() {
                            const oldName = $(this).attr('name');
                            const newName = oldName.replace(/\[attributes\]\[\d+\]/,
                                '[attributes][' + aIdx + ']');
                            $(this).attr('name', newName);
                        });
                        // ✅ UPDATE: Fix POR checkbox IDs after reindexing
                        const $porCheckbox = $(this).find('.por-checkbox');
                        const vIdx = $(this).closest('.variant-card').data('variant-index');
                        if ($porCheckbox.length) {
                            const newId = 'por_' + vIdx + '_' + aIdx;
                            $porCheckbox.attr('id', newId);
                            $porCheckbox.next('label').attr('for', newId);
                        }

                        const $rmBtn = $(this).find('.remove-variant-attr');
                        if (aIdx === 0) {
                            $rmBtn.addClass('d-none');
                        } else {
                            $rmBtn.removeClass('d-none');
                        }
                    });

                    const $rmVarBtn = $(this).find('.remove-variant');
                    if (idx === 0) {
                        $rmVarBtn.addClass('d-none');
                    } else {
                        $rmVarBtn.removeClass('d-none');
                    }
                });

                hydrateOldOptions();
                updateAllVariantCards();
                // enforceProductCodeTypeRule();

            }

            $(document).on('click', '#add-variant', function() {

                const $first = $('.variant-card:first');
                const $clone = $first.clone(false, false); // important

                // reset all inputs
                $clone.find('input[type=text], input[type=number]').val('');
                $clone.find('input[type=file]').val('');
                $clone.find('select').val('');

                // remove errors
                $clone.find('.invalid-feedback').remove();
                $clone.find('.is-invalid').removeClass('is-invalid');

                // reset attribute rows
                const $attrWrapper = $clone.find('.variant-attr-wrapper');
                const $firstAttr = $attrWrapper.find('.variant-attr-row:first').clone(false, false);

                $firstAttr.find('input[type=text], input[type=number]').val('');
                $firstAttr.find('input[type=file]').val('');
                $firstAttr.find('select').val('');

                $attrWrapper.html($firstAttr);

                $('#variants-wrapper').append($clone);
                reindexVariants();

                resetPorState($clone);

            });
            $(document).on('click', '.remove-variant', function() {
                if ($('.variant-card').length <= 1) return;

                Swal.fire({
                    title: 'Remove Variant?',
                    text: 'This will remove the variant and all its attributes.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, remove it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).closest('.variant-card').remove();
                        reindexVariants();
                    }
                });
            });

            $(document).on('click', '.add-variant-attr', function() {

                const $wrapper = $(this).closest('.variant-card').find('.variant-attr-wrapper');
                const $first = $wrapper.find('.variant-attr-row:first');
                const $clone = $first.clone(false, false);

                $clone.find('input[type=text], input[type=number]').val('');
                $clone.find('input[type=file]').val('');
                $clone.find('select').val('');

                $clone.find('.invalid-feedback').remove();
                $clone.find('.is-invalid').removeClass('is-invalid');

                $wrapper.append($clone);
                reindexVariants();

                resetPorState($clone);

            });

            $(document).on('click', '.remove-variant-attr', function() {
                const $wrapper = $(this).closest('.variant-attr-wrapper');
                if ($wrapper.find('.variant-attr-row').length <= 1) return;

                const $row = $(this).closest('.variant-attr-row');

                Swal.fire({
                    title: 'Remove Color?',
                    text: 'This will remove this color option permanently.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, remove it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $row.remove();
                        reindexVariants();
                    }
                });
            });

            /* ================================================================
               ATTRIBUTE OPTIONS LOADING
               ================================================================ */
            function fillOptions($select, attrId, currentVal) {
                $select.html('<option value="">Select Option</option>');
                if (!attrId || !optionMap[attrId]) return;

                optionMap[attrId].forEach(opt => {
                    const selected = currentVal && String(opt.id) === String(currentVal) ? 'selected' : '';
                    $select.append(`<option value="${opt.id}" ${selected}>${opt.name}</option>`);
                });
            }

            $(document).on('change', '.variant-attr-select', function() {
                const attrId = $(this).val();
                const $optSelect = $(this).closest('.variant-attr-row').find('.variant-attr-option');
                fillOptions($optSelect, attrId, null);
            });

            function hydrateOldOptions() {
                $('.variant-attr-row').each(function() {
                    const $row = $(this);
                    const attrId = $row.find('.variant-attr-select').val();
                    const optVal = $row.find('.variant-attr-option').val();

                    if (!attrId) {
                        $row.find('.variant-attr-option').html('<option value="">Select Option</option>');
                        return;
                    }

                    fillOptions($row.find('.variant-attr-option'), attrId, optVal);
                });
            }

            // Set initial state on page load
            const initialType = $('#product_type').val();
            if (initialType === '1') {
                showSection('simple');
            } else if (initialType === '3') {
                showSection('variant');
            } else {
                toggleSectionFields('none');
            }

            hydrateOldOptions();

            /* ================================================================
               IMAGE SIZE VALIDATION (600x600)
               ================================================================ */
            function checkImage600(file) {
                return new Promise((resolve) => {
                    const img = new Image();
                    const url = URL.createObjectURL(file);

                    img.onload = function() {
                        URL.revokeObjectURL(url);
                        resolve(img.width === 600 && img.height === 600);
                    };

                    img.onerror = function() {
                        URL.revokeObjectURL(url);
                        resolve(false);
                    };

                    img.src = url;
                });
            }

            /* ================================================================
               VALIDATION HELPERS
               ================================================================ */
            function clearJsErrors() {
                $('.is-invalid').removeClass('is-invalid');
                $('.js-image-error, .js-sell-error, .js-attr-error, .js-opt-error').remove();
            }

            function showJsError($input, message) {
                $input.addClass('is-invalid');
                if ($input.next('.js-image-error, .js-sell-error').length === 0) {
                    $input.after(
                        `<small class="text-danger ${$input.attr('type') === 'file' ? 'js-image-error' : 'js-sell-error'}">${message}</small>`
                    );
                }
            }

            function validateFrontend() {
                clearJsErrors();
                let ok = true;

                const $title = $('input[name="title"]');
                const $category = $('select[name="category_id"]');
                const $type = $('#product_type');
                const $shortDesc = $('input[name="short_description"]');
                const $mainImg = $('input[name="images"]');
                const $unit = $('select[name="unit_id"]');

                if (!$title.val()) {
                    showJsError($title, 'Product title is required.');
                    ok = false;
                }
                if (!$category.val()) {
                    showJsError($category, 'Category is required.');
                    ok = false;
                }
                if (!$type.val()) {
                    showJsError($type, 'Product mode is required.');
                    ok = false;
                }
                if (!$shortDesc.val()) {
                    showJsError($shortDesc, 'Short description is required.');
                    ok = false;
                }
                if (!$mainImg.val()) {
                    showJsError($mainImg, 'Main image is required.');
                    ok = false;
                }
                if (!$unit.val()) {
                    showJsError($unit, 'Unit is required.');
                    ok = false;
                }

                if ($type.val() === '1') {
                    const $price = $('input[name="simple[price]"]');
                    const $sell = $('input[name="simple[sale_price]"]');

                    if (!$price.val()) {
                        showJsError($price, 'MRP is required.');
                        ok = false;
                    }

                    const priceVal = parseFloat($price.val());
                    const sellVal = parseFloat($sell.val());

                    if ($sell.val() !== '' && !isNaN(priceVal) && !isNaN(sellVal)) {
                        if (sellVal >= priceVal) {
                            showJsError($sell, 'Sell price must be less than MRP.');
                            ok = false;
                        }
                    }
                }

                if ($type.val() === '3') {

                    let anyProductCodeSelected = false;
                    $('.variant-card').each(function() {
                        const val = $(this).find('.product-code-type-select').val();
                        if (val && val !== '') {
                            anyProductCodeSelected = true;
                        }
                    });



                    $('.variant-card').each(function() {


                        const $vname = $(this).find('.variant-name-select');
                        const $sku = $(this).find('input[name$="[sku]"]');
                        const $vimg = $(this).find('input[name$="[image]"]').first();

                        const $productCode = $(this).find('.product-code-type-select');


                        if (anyProductCodeSelected && (!$productCode.val() || $productCode.val() === '')) {
                            showJsError($productCode, 'Product Code Type is required for all variants.');
                            ok = false;
                        }


                        if (!$vname.val()) {
                            showJsError($vname, 'Variant type is required.');
                            ok = false;
                        }
                        if (!$sku.val()) {
                            showJsError($sku, 'SKU is required.');
                            ok = false;
                        }
                        if ($vimg.length && !$vimg.val()) {
                            showJsError($vimg, 'Variant image is required.');
                            ok = false;
                        }

                        $(this).find('.variant-attr-row').each(function() {
                            const $attr = $(this).find('.variant-attr-select');
                            const $opt = $(this).find('.variant-attr-option');
                            const $amrp = $(this).find('input[name$="[mrp]"]');
                            const $asell = $(this).find('input[name$="[sell_price]"]');
                            const $aimg = $(this).find('input[name$="[image]"]');

                            if (!$attr.val() || String($attr.val()).trim() === '') {
                                showJsError($attr, 'Attribute is required.');
                                ok = false;
                            }
                            if (!$opt.val() || String($opt.val()).trim() === '') {
                                showJsError($opt, 'Attribute Value is required.');
                                ok = false;
                            }
                            // ✅ UPDATED: Only require MRP if POR is not checked
                            const $porCheckbox = $(this).find('.por-checkbox');
                            const isPorChecked = $porCheckbox.is(':checked');

                            if (!isPorChecked && !$amrp.val()) {
                                showJsError($amrp, 'MRP is required when POR is not checked.');
                                ok = false;
                            }

                            const mrpVal = parseFloat($amrp.val());
                            const sellVal = parseFloat($asell.val());

                            if ($asell.val() !== '') {
                                if (isNaN(sellVal)) {
                                    showJsError($asell, 'Invalid sell price.');
                                    ok = false;
                                } else if (!isNaN(mrpVal) && sellVal >= mrpVal) {
                                    showJsError($asell, 'Sell price must be less than MRP.');
                                    ok = false;
                                }
                            }

                            if (!$aimg.val()) {
                                showJsError($aimg, 'Image is required.');
                                ok = false;
                            }
                        });
                    });
                }

                return ok;
            }

            function resetPorState($context) {

                $context.find('.variant-attr-row').each(function() {

                    const $row = $(this);
                    const $por = $row.find('.por-checkbox');
                    const $mrp = $row.find('.mrp-input');
                    const $sell = $row.find('.sell-price-input');
                    const $star = $row.find('.mrp-required-star');

                    // force unchecked
                    $por.prop('checked', false);

                    // enable fields
                    $mrp.prop('disabled', false)
                        .prop('required', true)
                        .val('')
                        .css('background-color', '');

                    $sell.prop('disabled', false)
                        .val('')
                        .css('background-color', '');

                    $star.show();
                });
            }

            /* ================================================================
               FORM SUBMISSION WITH IMAGE VALIDATION
               ================================================================ */
            $('#product-form').on('submit', async function(e) {
                e.preventDefault();

                clearJsErrors();

                const basicValid = validateFrontend();
                if (!basicValid) {
                    const $firstError = $('.is-invalid:first');
                    if ($firstError.length) {
                        $('html, body').animate({
                            scrollTop: $firstError.offset().top - 120
                        }, 400);
                    }
                    return false;
                }

                let imageValid = true;
                const imageInputs = $('#product-form input[type="file"]').filter(function() {
                    return this.files && this.files.length > 0;
                });

                for (let i = 0; i < imageInputs.length; i++) {
                    const input = imageInputs[i];
                    const $input = $(input);
                    const file = input.files[0];

                    const ok = await checkImage600(file);

                    if (!ok) {
                        $input.addClass('is-invalid');
                        if ($input.next('.js-image-error').length === 0) {
                            $input.after(
                                '<small class="text-danger js-image-error">Image size must be 600 × 600 pixels.</small>'
                            );
                        }
                        imageValid = false;
                    }
                }

                if (!imageValid) {
                    const $first = $('.js-image-error:first');
                    if ($first.length) {
                        $('html, body').animate({
                            scrollTop: $first.offset().top - 120
                        }, 400);
                    }
                    return false;
                }

                this.submit();
            });
        });
    </script>
@endpush
