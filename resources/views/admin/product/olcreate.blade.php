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
                        <small class="text-muted">Supports variants + attributes (all 3 cases)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data" id="product-form">
                @csrf

                <h5 class="text-primary mb-3"><i class="ik ik-info"></i> Product Information</h5>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Product Title *</label>
                        <input name="title" type="text" class="form-control shadow-sm" placeholder="Enter product name" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Category *</label>
                        <select name="category_id" class="form-control shadow-sm" required>
                            <option value="">Select Category</option>
                            @foreach ($categories as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Product Mode *</label>
                        <select name="product_type" id="product_type" class="form-control shadow-sm" required>
                            <option value="">Select</option>
                            <option value="variant">Variant Product (variants can have attributes)</option>
                            <option value="attribute_only">Attribute Only (no variants, price by attribute option)</option>
                            <option value="simple">Simple (no variants, no attributes)</option>
                        </select>
                        <small class="text-muted d-block mt-1">
                            Variant product can be with attributes OR without attributes (both supported).
                        </small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Short Description *</label>
                        <input name="short_description" type="text" class="form-control shadow-sm" placeholder="Enter short description" required>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label class="fw-bold">Description</label>
                    <textarea name="description" class="form-control shadow-sm" rows="4" placeholder="Product description"></textarea>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Main Image</label>
                        <input name="images" type="file" class="form-control shadow-sm" accept="image/*">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Gallery Images</label>
                        <input name="product_images[]" type="file" class="form-control shadow-sm" multiple accept="image/*">
                    </div>
                </div>

                <div id="simple-section" class="d-none">
                    <hr class="my-4">
                    <h5 class="text-primary mb-3"><i class="ik ik-tag"></i> Simple Product Price</h5>

                    <div class="row mb-3">
                        <div class="col-md-4 mb-2">
                            <label class="fw-bold">Regular Price (MRP)</label>
                            <input type="number" class="form-control shadow-sm" name="simple[mrp]" placeholder="MRP" step="0.01">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="fw-bold">Sale Price</label>
                            <input type="number" class="form-control shadow-sm" name="simple[price]" placeholder="Sale Price" step="0.01">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="fw-bold">Stock</label>
                            <input type="number" class="form-control shadow-sm" name="simple[stock]" placeholder="Stock">
                        </div>
                    </div>
                </div>

                <div id="attribute-only-section" class="d-none">
                    <hr class="my-4">
                    <h5 class="text-primary mb-3"><i class="ik ik-list"></i> Attribute Only (No Variants)</h5>
                    <small class="text-muted d-block mb-3">
                        Use when product has no variants but price depends on attribute option (e.g. Size).
                    </small>

                    <div id="attribute-price-wrapper">

                        <div class="attribute-price-row border rounded p-3 mb-3 bg-light">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="fw-bold">Attribute *</label>
                                    <select name="attribute_prices[0][attribute_id]" class="form-control shadow-sm attribute-select" required>
                                        <option value="">Select Attribute</option>
                                        @foreach($attributes as $attr)
                                            <option value="{{ $attr->id }}">{{ $attr->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="fw-bold">Option *</label>
                                    <select name="attribute_prices[0][attribute_option_id]" class="form-control shadow-sm option-select" required>
                                        <option value="">Select Option</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="fw-bold">Price *</label>
                                    <input type="number" name="attribute_prices[0][price]" class="form-control shadow-sm" step="0.01" required>
                                </div>

                                <div class="col-md-2">
                                    <label class="fw-bold">Stock</label>
                                    <input type="number" name="attribute_prices[0][stock]" class="form-control shadow-sm">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-2">
                                <button type="button" class="btn btn-sm btn-danger remove-attribute-price d-none">
                                    <i class="ik ik-trash-2"></i> Remove
                                </button>
                            </div>
                        </div>

                    </div>

                    <button type="button" id="add-attribute-price" class="btn btn-outline-primary shadow-sm">
                        <i class="ik ik-plus"></i> Add Attribute Price Row
                    </button>
                </div>

                <div id="variant-section" class="d-none">
                    <hr class="my-4">
                    <h5 class="text-primary mb-3"><i class="ik ik-layers"></i> Variants</h5>
                    <small class="text-muted d-block mb-3">
                        Variant can have attributes OR can be without attributes (both supported).
                    </small>

                    <div id="variants-wrapper">

                        <div class="variant-card border rounded p-3 mb-4 shadow-sm bg-light" data-variant-index="0">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 text-dark">Variant <span class="variant-index-label">1</span></h6>
                                <button type="button" class="btn btn-sm btn-danger remove-variant d-none">
                                    <i class="ik ik-trash-2"></i> Remove
                                </button>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-4 mb-2">
                                    <label class="fw-bold">Catalog No.</label>
                                    <input type="text" class="form-control shadow-sm" name="variants[0][catalog_number]" placeholder="Catalog No">
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label class="fw-bold">SKU</label>
                                    <input type="text" class="form-control shadow-sm" name="variants[0][sku]" placeholder="SKU">
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label class="fw-bold">Body Color</label>
                                    <select name="variants[0][body_color]" class="form-control shadow-sm">
                                        <option value="">Select Body Color</option>
                                        @foreach ($colors as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-3 mb-2">
                                    <label class="fw-bold">MRP</label>
                                    <input type="number" class="form-control shadow-sm" name="variants[0][mrp]" step="0.01">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="fw-bold">Sale Price</label>
                                    <input type="number" class="form-control shadow-sm" name="variants[0][price]" step="0.01">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="fw-bold">Stock</label>
                                    <input type="number" class="form-control shadow-sm" name="variants[0][stock]">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="fw-bold">Variant Image</label>
                                    <input type="file" class="form-control shadow-sm" name="variants[0][image]" accept="image/*">
                                </div>
                            </div>

                            <div class="border rounded p-3 bg-white mt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Variant Attributes (Optional)</h6>
                                    <button type="button" class="btn btn-sm btn-outline-primary add-variant-attr">
                                        <i class="ik ik-plus"></i> Add Attribute
                                    </button>
                                </div>

                                <div class="variant-attr-wrapper mt-3">

                                    <div class="variant-attr-row row g-2 align-items-end" data-attr-index="0">
                                        <div class="col-md-4">
                                            <label class="fw-bold">Attribute</label>
                                            <select name="variants[0][attributes][0][attribute_id]" class="form-control shadow-sm variant-attr-select">
                                                <option value="">Select Attribute</option>
                                                @foreach($attributes as $attr)
                                                    <option value="{{ $attr->id }}">{{ $attr->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="fw-bold">Option</label>
                                            <select name="variants[0][attributes][0][attribute_option_id]" class="form-control shadow-sm variant-attr-option">
                                                <option value="">Select Option</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="fw-bold">Value (Optional)</label>
                                            <input type="text" name="variants[0][attributes][0][attribute_value]" class="form-control shadow-sm" placeholder="If custom value">
                                        </div>

                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-sm btn-danger remove-variant-attr d-none">
                                                <i class="ik ik-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>

                    <button type="button" id="add-variant" class="btn btn-outline-primary shadow-sm">
                        <i class="ik ik-plus"></i> Add Variant
                    </button>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary shadow-sm">
                        <i class="ik ik-save"></i> Save Product
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection

@push('script')
<script>
$(document).ready(function () {

    const attributeOptionsMap = @json(
        $attributes->mapWithKeys(function($a){
            return [$a->id => $a->options->map(function($o){
                return ['id' => $o->id, 'name' => $o->name];
            })->values()];
        })
    );

    function fillOptions($optionSelect, attributeId) {
        $optionSelect.html('<option value="">Select Option</option>');
        if (!attributeId || !attributeOptionsMap[attributeId]) return;

        attributeOptionsMap[attributeId].forEach(function (opt) {
            $optionSelect.append('<option value="'+opt.id+'">'+opt.name+'</option>');
        });
    }

    function resetSections() {
        $('#variant-section').addClass('d-none');
        $('#attribute-only-section').addClass('d-none');
        $('#simple-section').addClass('d-none');
    }

    $('#product_type').on('change', function () {
        resetSections();

        const type = $(this).val();

        if (type === 'variant') {
            $('#variant-section').removeClass('d-none');
        }

        if (type === 'attribute_only') {
            $('#attribute-only-section').removeClass('d-none');
        }

        if (type === 'simple') {
            $('#simple-section').removeClass('d-none');
        }
    });

    let variantIndex = 1;

    $('#add-variant').on('click', function () {
        const $first = $('.variant-card:first');
        const $clone = $first.clone();

        $clone.attr('data-variant-index', variantIndex);
        $clone.find('.variant-index-label').text(variantIndex + 1);
        $clone.find('.remove-variant').removeClass('d-none');

        $clone.find('input, select, textarea').each(function () {
            const oldName = $(this).attr('name');
            if (!oldName) return;

            const newName = oldName.replace(/^variants\[\d+\]/, 'variants[' + variantIndex + ']');
            $(this).attr('name', newName);

            if ($(this).is('input[type="file"]')) {
                $(this).val('');
            } else {
                $(this).val('');
            }
        });

        const $attrWrapper = $clone.find('.variant-attr-wrapper');
        const $firstAttrRow = $attrWrapper.find('.variant-attr-row:first');
        $attrWrapper.html('');
        const $newAttrRow = $firstAttrRow.clone();

        $newAttrRow.attr('data-attr-index', 0);
        $newAttrRow.find('select, input').each(function () {
            const oldName = $(this).attr('name');
            if (!oldName) return;
            const newName = oldName
                .replace(/^variants\[\d+\]/, 'variants[' + variantIndex + ']')
                .replace(/\[attributes]\[\d+]/, '[attributes][0]');
            $(this).attr('name', newName).val('');
        });

        $newAttrRow.find('.remove-variant-attr').addClass('d-none');
        $newAttrRow.find('.variant-attr-option').html('<option value="">Select Option</option>');
        $attrWrapper.append($newAttrRow);

        $('#variants-wrapper').append($clone);

        variantIndex++;
    });

    $(document).on('click', '.remove-variant', function () {
        $(this).closest('.variant-card').remove();

        $('#variants-wrapper .variant-card').each(function (i) {
            $(this).attr('data-variant-index', i);
            $(this).find('.variant-index-label').text(i + 1);

            $(this).find('input, select, textarea').each(function () {
                const oldName = $(this).attr('name');
                if (!oldName) return;

                const newName = oldName.replace(/^variants\[\d+\]/, 'variants[' + i + ']');
                $(this).attr('name', newName);
            });

            $(this).find('.variant-attr-row').each(function (j) {
                $(this).attr('data-attr-index', j);
                $(this).find('input, select').each(function () {
                    const oldName = $(this).attr('name');
                    if (!oldName) return;

                    const newName = oldName
                        .replace(/^variants\[\d+\]/, 'variants[' + i + ']')
                        .replace(/\[attributes]\[\d+]/, '[attributes][' + j + ']');
                    $(this).attr('name', newName);
                });
            });

            if (i === 0) $(this).find('.remove-variant').addClass('d-none');
            else $(this).find('.remove-variant').removeClass('d-none');
        });
    });

    $(document).on('click', '.add-variant-attr', function () {
        const $variantCard = $(this).closest('.variant-card');
        const vIndex = $variantCard.attr('data-variant-index');
        const $wrapper = $variantCard.find('.variant-attr-wrapper');

        const $last = $wrapper.find('.variant-attr-row:last');
        const nextAttrIndex = parseInt($last.attr('data-attr-index') || 0) + 1;

        const $clone = $last.clone();
        $clone.attr('data-attr-index', nextAttrIndex);
        $clone.find('select, input').val('');

        $clone.find('select, input').each(function () {
            const oldName = $(this).attr('name');
            if (!oldName) return;

            const newName = oldName
                .replace(/^variants\[\d+\]/, 'variants[' + vIndex + ']')
                .replace(/\[attributes]\[\d+]/, '[attributes][' + nextAttrIndex + ']');
            $(this).attr('name', newName);
        });

        $clone.find('.remove-variant-attr').removeClass('d-none');
        $clone.find('.variant-attr-option').html('<option value="">Select Option</option>');

        $wrapper.append($clone);
    });

    $(document).on('click', '.remove-variant-attr', function () {
        const $variantCard = $(this).closest('.variant-card');
        const $wrapper = $variantCard.find('.variant-attr-wrapper');

        $(this).closest('.variant-attr-row').remove();

        $wrapper.find('.variant-attr-row').each(function (j) {
            $(this).attr('data-attr-index', j);
            $(this).find('input, select').each(function () {
                const oldName = $(this).attr('name');
                if (!oldName) return;

                const vIndex = $variantCard.attr('data-variant-index');
                const newName = oldName
                    .replace(/^variants\[\d+\]/, 'variants[' + vIndex + ']')
                    .replace(/\[attributes]\[\d+]/, '[attributes][' + j + ']');
                $(this).attr('name', newName);
            });

            if (j === 0) $(this).find('.remove-variant-attr').addClass('d-none');
            else $(this).find('.remove-variant-attr').removeClass('d-none');
        });
    });

    $(document).on('change', '.variant-attr-select', function () {
        const attributeId = $(this).val();
        const $row = $(this).closest('.variant-attr-row');
        const $opt = $row.find('.variant-attr-option');
        fillOptions($opt, attributeId);
    });

    let attrPriceIndex = 1;

    $('#add-attribute-price').on('click', function () {
        const $first = $('.attribute-price-row:first');
        const $clone = $first.clone();

        $clone.find('.remove-attribute-price').removeClass('d-none');

        $clone.find('input, select').each(function () {
            const oldName = $(this).attr('name');
            if (!oldName) return;

            const newName = oldName.replace(/^attribute_prices\[\d+]/, 'attribute_prices[' + attrPriceIndex + ']');
            $(this).attr('name', newName).val('');
        });

        $clone.find('.option-select').html('<option value="">Select Option</option>');

        $('#attribute-price-wrapper').append($clone);
        attrPriceIndex++;
    });

    $(document).on('click', '.remove-attribute-price', function () {
        $(this).closest('.attribute-price-row').remove();

        $('#attribute-price-wrapper .attribute-price-row').each(function (i) {
            $(this).find('input, select').each(function () {
                const oldName = $(this).attr('name');
                if (!oldName) return;

                const newName = oldName.replace(/^attribute_prices\[\d+]/, 'attribute_prices[' + i + ']');
                $(this).attr('name', newName);
            });

            if (i === 0) $(this).find('.remove-attribute-price').addClass('d-none');
            else $(this).find('.remove-attribute-price').removeClass('d-none');
        });
    });

    $(document).on('change', '.attribute-select', function () {
        const attributeId = $(this).val();
        const $row = $(this).closest('.attribute-price-row');
        const $opt = $row.find('.option-select');
        fillOptions($opt, attributeId);
    });

    $('#product-form').on('submit', function (e) {
        const type = $('#product_type').val();

        if (!type) return true;

        if (type === 'variant') {
            if ($('#variants-wrapper .variant-card').length < 1) {
                e.preventDefault();
                alert('Please add at least 1 variant.');
                return false;
            }
        }

        if (type === 'attribute_only') {
            if ($('#attribute-price-wrapper .attribute-price-row').length < 1) {
                e.preventDefault();
                alert('Please add at least 1 attribute price row.');
                return false;
            }
        }

        return true;
    });

});
</script>
@endpush
  