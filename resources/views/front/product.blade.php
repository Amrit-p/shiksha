    @extends('front/layout')
    @section('title', $product->title)

    <style>
        .qty-wrapper {
            display: inline-flex;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }

        .qty-btn {
            width: 35px;
            height: 35px;
            border: none;
            background: #f5f5f5;
            font-size: 20px;
            cursor: pointer;
        }

        .qty-btn:hover {
            background: #ddd;
        }

        #qty {
            width: 50px;
            height: 35px;
            text-align: center;
            border: 1px solid;
            font-size: 16px;
        }

        .aa-prod-view-size a {
            padding: 6px 12px;
            border: 1px solid #ddd;
            margin: 4px;
            display: inline-block;
            cursor: pointer;
        }

        .aa-prod-view-size a.active {
            background: var(--brand-secondary);
            color: #fff;
            border-color: var(--brand-secondary);
        }

        .aa-color-wrapper {
            display: flex;
            gap: 10px;
        }

        .aa-prod-quantity {
            margin: 13px 0px;
        }

        .color-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid #ccc;
            cursor: pointer;
        }

        .color-circle.active {
            border-color: var(--brand-secondary);
        }

        .disabled {
            pointer-events: none;
            opacity: .4;
            text-decoration: line-through;
        }

        /* ── Selector rows ── */
        .selector-row {
            margin-bottom: 14px;
        }

        .selector-row h5 {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .variant-wrapper,
        .attribute-wrapper,
        .custom-field-wrapper {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .variantBtn,
        .attrBtn,
        .cfBtn {
            min-width: 80px;
            border: 1px solid #ccc;
            padding: 8px 14px;
            background: #fff;
            cursor: pointer;
            font-size: 13px;
        }

        .variantBtn.active,
        .attrBtn.active,
        .cfBtn.active {
            background: var(--brand-primary);
            color: #fff;
            border-color: var(--brand-primary);
        }

        .variantBtn:disabled,
        .attrBtn:disabled,
        .cfBtn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
            text-decoration: line-through;
        }

        /* ── POR ── */
        #porInputBlock {
            background: #fff5fb;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #f5bddf;
            margin-top: 10px;
        }

        #porInputBlock h6 {
            color: var(--brand-primary);
            margin-bottom: 10px;
            font-weight: 600;
        }

        #porInputBlock .form-control {
            border: 1px solid var(--brand-accent);
        }

        #porInputBlock .form-control:focus {
            border-color: var(--brand-secondary);
            box-shadow: 0 0 0 0.2rem rgba(24, 169, 230, 0.25);
        }

        #porInputBlock small {
            color: #7b4a6e;
            display: block;
            margin-top: 5px;
        }

        .custom-field-wrapper {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
    </style>

    @section('container')
        @php
            $isSimple = (int) $product->product_type === 1;
            $isVariant = (int) $product->product_type === 3;
        @endphp

        <section id="aa-product-details">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="aa-product-details-area">
                            <div class="aa-product-details-content">
                                <div class="row">

                                    <div class="col-md-5">
                                        <img id="mainProductImage" src="{{ asset('storage/' . $product->image) }}"
                                            alt="{{ $product->title }}"
                                            style="width:100%;max-height:420px;object-fit:contain;border:1px solid #ddd;">
                                    </div>

                                    <div class="col-md-7">
                                        <h3>{{ $product->title }}</h3>

                                        @if ($isSimple)
                                            <p style="font-size: 18px; font-weight: bold;">
                                                @if ($product->sale_price && $product->sale_price < $product->price)
                                                    Rs {{ number_format($product->sale_price, 2) }}
                                                    <del style="margin-left:8px;color:#999;">
                                                        Rs {{ number_format($product->price, 2) }}
                                                    </del>
                                                @else
                                                    Rs {{ number_format($product->price, 2) }}
                                                @endif
                                            </p>
                                        @else
                                            <p id="priceBlock" style="font-size: 18px; font-weight: bold;">
                                                <span id="sellPrice"></span>
                                                <del id="mrpPrice" style="margin-left:8px;color:#999;"></del>
                                            </p>
                                        @endif

                                        <p>
                                            Availability:
                                            <span id="showStockText">
                                                @if ($isSimple)
                                                    @if ((int) ($product->stock ?? 0) > 0)
                                                        In Stock ({{ (int) $product->stock }} available)
                                                    @else
                                                        <span class="text-danger">Out of stock</span>
                                                    @endif
                                                @else
                                                    {{-- Variant: set by JS when option selected --}}
                                                    <span class="text-muted">Select option to see availability</span>
                                                @endif
                                            </span>
                                        </p>

                                        {{-- <p>{{ $product->short_description }}</p> --}}
                                        {{-- Variant Description --}}
                                        <div id="variantDescriptionBlock" style="margin-top:15px; display:none;">
                                            <h5 style="font-weight:600;">Description</h5>
                                            <p id="variantDescriptionText" style="color:#555;"></p>
                                        </div>

                                        @if ($isVariant)

                                            {{-- ══════════════════════════════════════════
                                                STEP 1: PRODUCT CODE TYPE
                                                Only rendered when product has code types
                                                ══════════════════════════════════════════ --}}
                                            @if ($hasCodeTypes ?? false)
                                                <div class="selector-row" id="codeTypeRow">
                                                    <h5>Select Type</h5>
                                                    <div class="variant-wrapper" id="codeTypeWrapper">
                                                        @foreach ($codeTypes as $idx => $ct)
                                                            <button type="button"
                                                                class="variantBtn codeTypeBtn {{ $idx === 0 ? 'active' : '' }}"
                                                                data-code-type-id="{{ $ct['id'] }}"
                                                                data-variant-ids="{{ json_encode($ct['variant_ids']) }}">
                                                                {{ $ct['name'] }}
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- ══════════════════════════════════════════
                                                STEP 2: VARIANT TYPE (dynamically populated)
                                                ══════════════════════════════════════════ --}}
                                            <div class="selector-row" id="variantRow">
                                                <h5>Select Variant</h5>
                                                <div class="variant-wrapper" id="variantWrapper">
                                                    {{-- Buttons will be rendered by JavaScript --}}
                                                </div>
                                            </div>

                                            {{-- ══════════════════════════════════════════
                                                STEPS 3…N: CUSTOM FIELDS  (JS-injected)
                                                Each custom field gets its own selector row
                                                ══════════════════════════════════════════ --}}
                                            <div id="customFieldSteps"></div>

                                            {{-- ══════════════════════════════════════════
                                                LAST STEP: ATTRIBUTE
                                                ══════════════════════════════════════════ --}}
                                            <div class="selector-row" id="attributeBlock" style="display:none;">
                                                <h5>Select Option</h5>
                                                <div id="attributeList" class="attribute-wrapper"></div>
                                            </div>

                                            {{-- POR input --}}
                                            <div id="porInputBlock" style="display:none;">
                                                <h6>💬 Price on Request</h6>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="porPrice">Enter Your Price <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="number" id="porPrice" class="form-control"
                                                                placeholder="Enter price" min="0" step="0.01">
                                                            <small>This is a temporary price for this order</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        @endif

                                        {{-- Quantity --}}
                                        <div class="aa-prod-quantity">
                                            <div class="qty-wrapper">
                                                <button type="button" class="qty-btn" onclick="changeQty(-1)">−</button>

                                                <input type="number" id="qty" name="qty" value="1"
                                                    min="1" max="10" oninput="qtyChanged()">

                                                <button type="button" class="qty-btn" onclick="changeQty(1)">+</button>
                                            </div>
                                            @if ($product->unit)
                                                <div class="text-muted" style="font-size:14px; margin-top:4px;">
                                                    Quantity is calculated in <strong>{{ $product->unit->name }}</strong>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="aa-prod-view-bottom">
                                            <button id="addToCartBtn" type="button" class="aa-add-to-cart-btn"
                                                style="padding: 10px 30px; font-size: 14px;">
                                                ADD TO CART
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-md-12  mt-6">
                                        <hr>
                                        <p>{{ $product->description }}</p>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </section>

        <form id="frmAddToCart" method="POST" action="{{ route('front.add_to_cart') }}">
            @csrf
            <input type="hidden" id="product_id" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="variant_id" id="variant_id">
            <input type="hidden" name="variant_attribute_id" id="variant_attribute_id">
            <input type="hidden" name="pqty" id="pqty">
            <input type="hidden" name="is_por" id="is_por" value="0">
            <input type="hidden" name="por_price" id="por_price" value="">
        </form>

        @if ($isVariant)
            <script>
                window.__PRODUCT_VARIANTS__ = @json($variantsJson);
                window.__HAS_CODE_TYPES__ = @json($hasCodeTypes ?? false);
                window.__DISABLED_ATTR_IDS__ = @json($disabledAttributeIds ?? []);
            </script>
        @endif

        <script>
            /* ================================================================
                                STATE OBJECT (tracks user selections throughout the cascade)
                                ================================================================ */
            var S = {
                codeTypeId: null,
                variantId: null,
                variant: null,
                cfValues: {},
                attributeId: null
            };

            /* ================================================================
            UTILITY FUNCTIONS
            ================================================================ */
            function setActive(wrapper, btn) {
                wrapper.querySelectorAll('button').forEach(function(b) {
                    b.classList.remove('active');
                });
                btn.classList.add('active');
            }

            function hideEl(id) {
                var el = document.getElementById(id);
                if (el) el.style.display = 'none';
            }

            function showEl(id) {
                var el = document.getElementById(id);
                if (el) el.style.display = '';
            }

            function setMainImage(url) {
                document.getElementById('mainProductImage').src = url;
            }

            function resetPriceArea() {
                document.getElementById('sellPrice').innerText = '';
                document.getElementById('mrpPrice').innerText = '';
                document.getElementById('mrpPrice').style.display = 'none';
            }

            function updatePrice(mrp, sell) {
                var sellEl = document.getElementById('sellPrice');
                var mrpEl = document.getElementById('mrpPrice');

                mrp = parseFloat(mrp) || 0;
                sell = parseFloat(sell) || 0;

                if (sell > 0 && sell < mrp) {
                    sellEl.innerText = 'Rs ' + sell.toFixed(2);
                    mrpEl.innerText = 'Rs ' + mrp.toFixed(2);
                    mrpEl.style.display = 'inline';
                } else {
                    sellEl.innerText = 'Rs ' + mrp.toFixed(2);
                    mrpEl.style.display = 'none';
                }
            }

            function hidePrice() {
                var block = document.getElementById('priceBlock');
                if (block) block.style.display = 'none';
            }

            function showPrice() {
                var block = document.getElementById('priceBlock');
                if (block) block.style.display = '';
            }

            function showPorInput() {
                var block = document.getElementById('porInputBlock');
                if (block) {
                    block.style.display = '';
                    document.getElementById('porPrice').focus();
                }
            }

            function hidePorInput() {
                var block = document.getElementById('porInputBlock');
                if (block) {
                    block.style.display = 'none';
                    document.getElementById('porPrice').value = '';
                }
            }

            /** Update "Availability" text from selected variant attribute stock (variant products only). */
            function updateAvailabilityText(attr) {
                var el = document.getElementById('showStockText');
                if (!el) return;
                if (!attr) {
                    el.innerHTML = '<span class="text-muted">Select option to see availability</span>';
                    return;
                }
                var stock = parseInt(attr.stock, 10) || 0;
                if (stock > 0) {
                    el.innerHTML = 'In Stock (' + stock + ' available)';
                } else {
                    el.innerHTML = '<span class="text-danger">Out of stock</span>';
                }
            }

            /** Type-safe check: is this variant_attribute id in the out-of-stock list? (handles number vs string from JSON) */
            function isAttrOutOfStock(attrId) {
                if (attrId == null || attrId === '') return false;
                var list = window.__DISABLED_ATTR_IDS__;
                if (!Array.isArray(list)) return false;
                return list.some(function(id) {
                    return id == attrId;
                });
            }

            /** Enable/disable Add to Cart button based on selected option stock (variant products). */
            function updateAddToCartButtonState(attrId) {
                var btn = document.getElementById('addToCartBtn');
                if (!btn) return;
                if (!attrId) {
                    btn.disabled = false;
                    return;
                }
                btn.disabled = isAttrOutOfStock(attrId);
                if (btn.disabled) btn.title = 'This option is out of stock';
                else btn.title = '';
            }

            /* ================================================================
            STEP 1 — CODE TYPE SELECTED (if product has code types)
            ================================================================ */
            function selectCodeType(btn) {
                var wrapper = document.getElementById('codeTypeWrapper');
                setActive(wrapper, btn);

                S.codeTypeId = parseInt(btn.dataset.codeTypeId);
                S.variantId = null;
                S.variant = null;
                S.cfValues = {};
                S.attributeId = null;

                // Reset downstream UI
                resetPriceArea();
                document.getElementById('customFieldSteps').innerHTML = '';
                hideEl('attributeBlock');
                document.getElementById('variant_attribute_id').value = '';
                updateAvailabilityText(null);
                updateAddToCartButtonState(null);

                // Filter and re-render variants for this code type
                populateVariantsForCodeType(btn.dataset.codeTypeId);
            }

            /* ================================================================
            GROUP VARIANTS BY NAME
            ─────────────────────────────────────────────────────────────
            Multiple DB variants can share the same variant_name
            (e.g. three "Size 20 mm" rows, each with one attribute).
            We group them into ONE button and merge all their attributes
            so the user sees:
                [Size 20 mm]  →  sub-options: One / Two / Three
            ================================================================ */

            /**
             * Build a "group" object from an array of raw variant objects
             * that share the same variant_name.
             * Returns { id, variant_name, image, custom_fields, attributes[] }
             */
            function buildVariantGroup(variants) {
                if (!variants || !variants.length) return null;

                var rep = variants[0];

                // Merge attributes from all variants (keep original mapping to variant id)
                var mergedAttrs = [];
                variants.forEach(function(v) {
                    (v.attributes || []).forEach(function(attr) {
                        attr._variantId = v.id;
                        mergedAttrs.push(attr);
                    });
                });

                // Merge custom fields from all variants (THIS IS THE MAIN FIX)
                // Each CF keeps its source variant id so we can bind selection back to correct variant
                var mergedCF = [];
                variants.forEach(function(v) {
                    (v.custom_fields || []).forEach(function(cf) {
                        var cloned = Object.assign({}, cf);
                        cloned._variantId = v.id; // important
                        mergedCF.push(cloned);
                    });
                });

                // Deduplicate custom fields by (type_id + value + variantId)
                // We keep per-variant uniqueness so One/Two/Three can coexist
                var seenCF = {};
                mergedCF = mergedCF.filter(function(cf) {
                    var key = String(cf.type_id) + '|' + String(cf.value) + '|' + String(cf._variantId);
                    if (seenCF[key]) return false;
                    seenCF[key] = true;
                    return true;
                });

                return {
                    id: rep.id,
                    variant_name: rep.variant_name,
                    variantname_id: rep.variantname_id || null,
                    image: rep.image,
                    custom_fields: mergedCF,
                    attributes: mergedAttrs,
                    variant_description: rep.variant_description || '',
                    _rawVariants: variants
                };
            }

            /**
             * Group a flat variants array by variant_name.
             * Returns an array of group objects (one per unique name).
             */
            function groupVariantsByName(variants) {
                var order = [];
                var groups = {};

                (variants || []).forEach(function(v) {
                    // Prefer variantname_id if available, else normalize name
                    var key = v.variantname_id ? ('id:' + v.variantname_id) : ('name:' + String(v.variant_name || '')
                        .trim());

                    if (!groups[key]) {
                        groups[key] = [];
                        order.push(key);
                    }
                    groups[key].push(v);
                });

                return order.map(function(key) {
                    return buildVariantGroup(groups[key]);
                });
            }


            /* ── Populate helpers ─────────────────────────────────────── */

            function populateVariantsForCodeType(codeTypeId) {
                var allVariants = (window.__PRODUCT_VARIANTS__ || []).filter(function(v) {
                    return v.product_code_type_id == codeTypeId;
                });
                renderVariantButtons(allVariants);
            }

            function populateAllVariants() {
                renderVariantButtons(window.__PRODUCT_VARIANTS__ || []);
            }

            /**
             * Render one button per unique variant_name group.
             * Clicking a button triggers selectVariantGroup() with the merged group.
             */
            function renderVariantButtons(variants) {
                var wrapper = document.getElementById('variantWrapper');
                wrapper.innerHTML = '';

                var groups = groupVariantsByName(variants);
                var firstBtn = null;

                groups.forEach(function(group) {
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'variantBtn';
                    btn.innerText = group.variant_name;

                    btn.addEventListener('click', function() {
                        setActive(wrapper, btn);
                        selectVariantGroup(group);
                    });

                    wrapper.appendChild(btn);
                    if (!firstBtn) firstBtn = btn;
                });

                if (firstBtn) firstBtn.click();
            }

            /* ================================================================
            STEP 2 — VARIANT GROUP SELECTED
            (called when user clicks a variant button)
            ================================================================ */
            function selectVariantGroup(group) {
                S.variantId = group.id; // representative variant id
                S.variant = group; // group object (has merged attributes)
                S.cfValues = {};
                S.attributeId = null;

                // The hidden field starts with the representative id;
                // it will be overwritten per-attribute when attr._variantId exists
                document.getElementById('variant_id').value = group.id;

                setMainImage(group.image);
                updateVariantDescription(group.variant_description || '');

                // Reset downstream
                resetPriceArea();
                hidePrice();
                hidePorInput();
                document.getElementById('customFieldSteps').innerHTML = '';
                hideEl('attributeBlock');
                document.getElementById('variant_attribute_id').value = '';
                updateAvailabilityText(null);
                updateAddToCartButtonState(null);

                // Step 3+: begin rendering custom field chain
                var groupedCF = groupCustomFieldsByIndex(group._rawVariants);
                renderCustomFieldStep(0, groupedCF);

                // renderCfStep(0);
                // renderCustomFieldsSingleRow(group.custom_fields); // ✅ NEW


            }

            function groupCustomFieldsByIndex(variants) {
                var steps = {};

                variants.forEach(function(v) {
                    (v.custom_fields || []).forEach(function(cf, index) {

                        if (!steps[index]) {
                            steps[index] = [];
                        }

                        steps[index].push({
                            value: cf.value,
                            variantId: v.id
                        });
                    });
                });

                return steps;
            }

            function renderCustomFieldStep(stepIndex, groupedCF) {

                var container = document.getElementById('customFieldSteps');

                // remove next steps
                Array.from(container.children).forEach(function(el) {
                    if (parseInt(el.dataset.step) >= stepIndex) {
                        el.remove();
                    }
                });

                if (!groupedCF[stepIndex]) {
                    renderAttributesForSelectedVariant();
                    return;
                }

                var currentVariantId = parseInt(
                    document.getElementById('variant_id').value || 0
                );

                var items = groupedCF[stepIndex] || [];

                // 🔥 CRITICAL FIX:
                // Step 0 → show all options
                // Step 1+ → show ONLY options belonging to selected variant
                if (stepIndex > 0) {
                    items = items.filter(function(x) {
                        return x.variantId == currentVariantId;
                    });
                }

                // If after filtering nothing left → go to attributes
                if (!items.length) {
                    renderAttributesForSelectedVariant();
                    return;
                }

                // Remove duplicates
                var seen = {};
                items = items.filter(function(x) {
                    var key = String(x.value || '').trim().toLowerCase();
                    if (!key) return false;
                    if (seen[key]) return false;
                    seen[key] = true;
                    return true;
                });

                var row = document.createElement('div');
                row.className = 'selector-row';
                row.dataset.step = stepIndex;

                var title = document.createElement('h5');
                title.innerText = 'Select Option';
                row.appendChild(title);

                var wrapper = document.createElement('div');
                wrapper.className = 'custom-field-wrapper';

                items.forEach(function(item) {

                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'cfBtn';
                    btn.innerText = item.value;

                    btn.onclick = function() {

                        setActive(wrapper, btn);

                        // Step 0 decides which variant
                        // if (stepIndex === 0) {
                        //     S.variantId = item.variantId;
                        //     document.getElementById('variant_id').value = item.variantId;
                        // }
                        if (stepIndex === 0) {
                            S.variantId = item.variantId;
                            document.getElementById('variant_id').value = item.variantId;

                            // 🔥 ADD THIS BLOCK
                            var selectedVariant = (window.__PRODUCT_VARIANTS__ || []).find(function(v) {
                                return v.id == item.variantId;
                            });

                            if (selectedVariant) {
                                updateVariantDescription(selectedVariant.variant_description || '');
                            }
                        }


                        renderCustomFieldStep(stepIndex + 1, groupedCF);
                    };

                    wrapper.appendChild(btn);
                });

                row.appendChild(wrapper);
                container.appendChild(row);

                // Auto-select first option
                var firstBtn = wrapper.querySelector('button');
                if (firstBtn) firstBtn.click();
            }

            function renderCustomFieldsSingleRow(customFields) {

                var stepsDiv = document.getElementById('customFieldSteps');
                stepsDiv.innerHTML = '';

                hideEl('attributeBlock'); // hide attributes until sub-option selected

                if (!customFields || customFields.length === 0) {
                    return;
                }

                var row = document.createElement('div');
                row.className = 'selector-row';

                var label = document.createElement('h5');
                label.innerText = 'Select Option';
                row.appendChild(label);

                var wrap = document.createElement('div');
                wrap.className = 'custom-field-wrapper';

                // show One | Two | Three only once (dedupe by value)
                var seen = {};
                customFields.forEach(function(cf) {
                    var v = String(cf.value || '').trim();
                    if (!v) return;

                    if (seen[v]) return;
                    seen[v] = true;

                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'cfBtn';
                    btn.innerText = v;

                    btn.addEventListener('click', function() {

                        setActive(wrap, btn);

                        // IMPORTANT: bind selected sub-option to its real variant id
                        if (cf._variantId) {
                            S.variantId = cf._variantId;
                            document.getElementById('variant_id').value = cf._variantId;
                        }

                        // now render attributes for ONLY this selected sub-option
                        renderAttributesForSelectedVariant();
                    });

                    wrap.appendChild(btn);
                });

                row.appendChild(wrap);
                stepsDiv.appendChild(row);
            }

            // function renderAttributesForSelectedVariant() {
            //     var group = S.variant;
            //     if (!group) return;

            //     var selectedVariantId = S.variantId || parseInt(document.getElementById('variant_id').value || 0);
            //     if (!selectedVariantId) {
            //         hideEl('attributeBlock');
            //         return;
            //     }

            //     var block = document.getElementById('attributeBlock');
            //     var list = document.getElementById('attributeList');

            //     document.getElementById('variant_attribute_id').value = '';
            //     S.attributeId = null;
            //     list.innerHTML = '';


            //     // FILTER: only attributes belonging to selected sub-option variant
            //     // var attrs = (group.attributes || []).filter(function(a) {
            //     //     return !a._variantId || a._variantId == selectedVariantId;
            //     // });
            //     var attrs = group.attributes || [];


            //     // If nothing found, hide block (means mapping is missing)
            //     if (!attrs.length) {
            //         hideEl('attributeBlock');
            //         return;
            //     }



            //     // DEDUPE: if attributes are same across variants, show only once
            //     var seen = {};
            //     attrs = attrs.filter(function(a) {
            //         var key = [
            //             a.label_name || '',
            //             a.label || '',
            //             String(parseFloat(a.mrp || 0)),
            //             String(parseFloat(a.sell_price || 0)),
            //             a.is_por ? '1' : '0',
            //             a.image || ''
            //         ].join('|');

            //         if (seen[key]) return false;
            //         seen[key] = true;
            //         return true;
            //     });

            //     showEl('attributeBlock');
            //     showPrice();
            //     hidePorInput();

            //     var firstSelectable = null;

            //     attrs.forEach(function(attr) {
            //         var btn = document.createElement('button');
            //         btn.type = 'button';
            //         btn.className = 'attrBtn';

            //         var priceTag = '';
            //         if (attr.is_por) {
            //             priceTag = ' (POR)';
            //         } else if ((parseFloat(attr.mrp) || 0) > 0) {
            //             var mrp = parseFloat(attr.mrp) || 0;
            //             var sp = parseFloat(attr.sell_price) || 0;
            //             var displayPrice = (sp > 0 && sp < mrp) ? sp : mrp;
            //             // priceTag = ' ₹' + displayPrice.toFixed(2);
            //         }
            //         // console.log(attr);

            //         btn.innerText = (attr.label_name+" "+(attr.label)|| attr.label || 'Option') + priceTag;

            //         // keep your disabled logic if you really want it (your current code had a bug)
            //         var isDisabled = Array.isArray(window.__DISABLED_ATTR_IDS__) && window.__DISABLED_ATTR_IDS__
            //             .includes(attr.id);
            //         if (isDisabled) {
            //             btn.disabled = false;
            //             btn.title = 'Out of stock';
            //         } else if (!firstSelectable) {
            //             firstSelectable = btn;
            //         }

            //         btn.addEventListener('click', function(event) {
            //             event.preventDefault();
            //             event.stopPropagation();

            //             setActive(list, btn);

            //             S.attributeId = attr.id;
            //             document.getElementById('variant_attribute_id').value = attr.id;
            //             // 🔥 UPDATE DESCRIPTION ON ATTRIBUTE CLICK
            //             if (attr._variantId) {
            //                 var selectedVariant = (window.__PRODUCT_VARIANTS__ || []).find(function(v) {
            //                     return v.id == attr._variantId;
            //                 });

            //                 if (selectedVariant) {
            //                     updateVariantDescription(selectedVariant.variant_description || '');
            //                 }
            //             }


            //             // IMPORTANT: keep variant_id as the selected sub-option’s variant id
            //             document.getElementById('variant_id').value = selectedVariantId;
            //             S.variantId = selectedVariantId;

            //             if (attr.image) setMainImage(attr.image);

            //             if (attr.is_por) {
            //                 hidePrice();
            //                 showPorInput();
            //                 document.getElementById('is_por').value = '1';
            //                 document.getElementById('por_price').value = '';
            //             } else {
            //                 hidePorInput();
            //                 showPrice();
            //                 updatePrice(attr.mrp, attr.sell_price);
            //                 document.getElementById('is_por').value = '0';
            //                 document.getElementById('por_price').value = '';
            //             }
            //         });

            //         list.appendChild(btn);
            //     });

            //     if (firstSelectable) firstSelectable.click();
            // }

            function renderAttributesForSelectedVariant() {

                var group = S.variant;
                if (!group) return;

                var selectedVariantId = S.variantId || parseInt(document.getElementById('variant_id').value || 0);

                var block = document.getElementById('attributeBlock');
                var list = document.getElementById('attributeList');

                document.getElementById('variant_attribute_id').value = '';
                S.attributeId = null;
                list.innerHTML = '';

                var attrs = [];

                /* ======================================================
                   CHECK: Custom field exist?
                   ====================================================== */

                var hasCustomField = false;

                if (group._rawVariants && group._rawVariants.length > 1) {
                    hasCustomField = group._rawVariants.some(function(v) {
                        return v.custom_fields && v.custom_fields.length > 0;
                    });
                }

                if (hasCustomField) {

                    // 🔥 Custom field exists → show only selected variant attributes
                    var selectedVariant = (window.__PRODUCT_VARIANTS__ || []).find(function(v) {
                        return v.id == selectedVariantId;
                    });

                    if (selectedVariant) {
                        attrs = selectedVariant.attributes || [];
                    }

                } else {

                    // ✅ No custom field → old behaviour
                    attrs = group.attributes || [];
                }

                if (!attrs.length) {
                    block.style.display = 'none';
                    return;
                }

                block.style.display = '';
                showPrice();
                updateAddToCartButtonState('');

                var firstSelectable = null;

                attrs.forEach(function(attr) {

                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'attrBtn';

                    var priceTag = '';
                    if (attr.is_por) {
                        priceTag = ' (POR)';
                    } else if (attr.mrp > 0) {
                        var dp = (attr.sell_price > 0 && attr.sell_price < attr.mrp) ? attr.sell_price : attr.mrp;
                        priceTag = ' Rs ' + (parseFloat(dp) || 0).toFixed(2);
                    }

                    // btn.innerText = (attr.label_name || attr.label || 'Option') + priceTag;
                    btn.innerText = (attr.label_name || attr.label || 'Option');
                    console.log(attr.label_name);

                    var isDisabled = isAttrOutOfStock(attr.id);
                    if (isDisabled) {
                        btn.disabled = true;
                        btn.title = 'Out of stock';
                        btn.classList.add('out-of-stock');
                    } else {
                        if (!firstSelectable) firstSelectable = btn;
                    }
                    console.log("swc", btn.innerText);

                    var stockLabel = (attr.stock != null && attr.stock > 0) ? ' (' + attr.stock + ' in stock)' : '';
                    // if (!isDisabled && stockLabel) btn.innerText += stockLabel;
                    if (!isDisabled && stockLabel) btn.innerText;


                    btn.addEventListener('click', function() {

                        setActive(list, btn);

                        S.attributeId = attr.id;
                        document.getElementById('variant_attribute_id').value = attr.id;

                        if (attr._variantId) {
                            document.getElementById('variant_id').value = attr._variantId;
                            S.variantId = attr._variantId;
                        }

                        if (attr.image) setMainImage(attr.image);

                        if (attr.is_por) {
                            hidePrice();
                            showPorInput();
                            document.getElementById('is_por').value = '1';
                            document.getElementById('por_price').value = '';
                        } else {
                            hidePorInput();
                            showPrice();
                            updatePrice(attr.mrp, attr.sell_price);
                            document.getElementById('is_por').value = '0';
                        }

                        updateAvailabilityText(attr);
                        updateAddToCartButtonState(attr.id);
                    });
                    console.log(btn);

                    list.appendChild(btn);
                });

                if (firstSelectable) {
                    firstSelectable.click();
                } else if (attrs.length > 0) {
                    updateAvailabilityText(attrs[0]);
                    updateAddToCartButtonState(attrs[0].id);
                }
            }



            // Keep old name as alias so any stray references don't break
            function selectVariant(btn) {
                var variant = (window.__PRODUCT_VARIANTS__ || []).find(function(v) {
                    return v.id == btn.dataset.id;
                });
                if (!variant) return;
                selectVariantGroup(buildVariantGroup([variant]));
            }

            /* ================================================================
            STEPS 3…N — CUSTOM FIELDS (chained, one step per field type)
            ================================================================ */
            function renderCfStep(stepIndex) {
                var variant = S.variant;

                // No custom fields at all → jump straight to attributes
                if (!variant || !variant.custom_fields || variant.custom_fields.length === 0) {
                    renderAttributes();
                    return;
                }

                var fieldTypes = getFieldTypes(variant.custom_fields);

                // All cf steps done → render attributes
                if (stepIndex >= fieldTypes.length) {
                    renderAttributes();
                    return;
                }

                var fieldType = fieldTypes[stepIndex];

                // Wipe any steps that were rendered from this index onward
                clearCfStepsFrom(stepIndex);

                // Build step container
                var stepsDiv = document.getElementById('customFieldSteps');
                var stepDiv = document.createElement('div');
                stepDiv.className = 'selector-row';
                stepDiv.id = 'cfStep_' + stepIndex;

                var label = document.createElement('h5');
                label.innerText = fieldType.name;
                stepDiv.appendChild(label);

                var btnWrap = document.createElement('div');
                btnWrap.className = 'custom-field-wrapper';
                stepDiv.appendChild(btnWrap);
                stepsDiv.appendChild(stepDiv);

                // Collect all entries for this field type from the variant
                var fields = variant.custom_fields.filter(function(cf) {
                    return cf.type_id == fieldType.id;
                });

                // Deduplicate by value (a variant might store the same value twice)
                var seenValues = {};
                fields = fields.filter(function(cf) {
                    if (seenValues[cf.value]) return false;
                    seenValues[cf.value] = true;
                    return true;
                });

                // Render button for each distinct value
                fields.forEach(function(field) {
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'cfBtn';
                    btn.innerText = field.value;

                    // btn.addEventListener('click', function () {
                    //     setActive(btnWrap, btn);

                    //     S.cfValues[fieldType.id] = field.value;

                    //     // Advance to next CF step
                    //     renderCfStep(stepIndex + 1);
                    // });

                    btn.addEventListener('click', function() {
                        setActive(btnWrap, btn);

                        S.cfValues[fieldType.id] = field.value;

                        // Bind variant_id to the variant that owns this CF value (fixes One/Two/Three mapping)
                        if (field._variantId) {
                            S.variantId = field._variantId;
                            document.getElementById('variant_id').value = field._variantId;
                        }

                        renderCfStep(stepIndex + 1);
                    });


                    btnWrap.appendChild(btn);
                });
            }

            /* ================================================================
            CUSTOM FIELD HELPERS
            ================================================================ */
            function getFieldTypes(customFields) {
                var typeMap = {};
                customFields.forEach(function(cf) {
                    if (!typeMap[cf.type_id]) {
                        typeMap[cf.type_id] = {
                            id: cf.type_id,
                            name: cf.type_name
                        };
                    }
                });
                var types = Object.values(typeMap);
                // Sort by id so order is stable
                types.sort(function(a, b) {
                    return a.id - b.id;
                });
                return types;
            }

            function clearCfStepsFrom(stepIndex) {
                var stepsDiv = document.getElementById('customFieldSteps');
                var steps = stepsDiv.querySelectorAll('[id^="cfStep_"]');
                steps.forEach(function(step) {
                    var stepNum = parseInt(step.id.split('_')[1]);
                    if (stepNum >= stepIndex) {
                        step.remove();
                    }
                });
            }

            /* ================================================================
            LAST STEP — RENDER ATTRIBUTES (FIX #2 & #3: Fixed POR and selection)
            ================================================================ */
            function renderAttributes() {
                var variant = S.variant;
                if (!variant) return;

                var block = document.getElementById('attributeBlock');
                var list = document.getElementById('attributeList');

                document.getElementById('variant_attribute_id').value = '';
                S.attributeId = null;
                list.innerHTML = '';
                updateAddToCartButtonState('');

                if (!variant.attributes || variant.attributes.length === 0) {
                    block.style.display = 'none';
                    return;
                }

                // ✅ FIX: Show block and price BEFORE rendering buttons
                showEl('attributeBlock');
                showPrice();
                var firstSelectable = null;

                variant.attributes.forEach(function(attr) {
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'attrBtn';

                    // Label:  "OptionValue ₹XX.XX"  or  "OptionValue (POR)"
                    var priceTag = '';
                    if (attr.is_por) {
                        priceTag = ' (POR)';
                    } else if (attr.mrp > 0) {
                        var displayPrice = (attr.sell_price > 0 && attr.sell_price < attr.mrp) ? attr.sell_price : attr
                            .mrp;
                        // priceTag = '₹' + displayPrice.toFixed(2);
                        priceTag = displayPrice.toFixed(2);

                    }
                    // btn.innerText = (attr.label || attr.label_name || 'Option') + priceTag;\
                    btn.innerText = (attr.label || attr.label_name || 'Option');
                    console.log(btn.innerText);


                    var isDisabled = isAttrOutOfStock(attr.id);

                    if (isDisabled) {
                        btn.disabled = true;
                        btn.title = 'Out of stock';
                        btn.classList.add('out-of-stock');
                    } else {
                        if (!firstSelectable) firstSelectable = btn;
                    }
                    // Show stock in label if available (e.g. "White ₹100 (5 in stock)")
                    var stockLabel = (attr.stock != null && attr.stock > 0) ? ' (' + attr.stock + ' in stock)' : '';
                    if (!isDisabled && stockLabel) btn.innerText += stockLabel;

                    btn.addEventListener('click', function(event) {
                        event.preventDefault();
                        event.stopPropagation();

                        setActive(list, btn);

                        S.attributeId = attr.id;
                        document.getElementById('variant_attribute_id').value = attr.id;

                        // ✅ KEY FIX: Update variant_id to the specific variant
                        // that owns this attribute (since group merges across variants)
                        if (attr._variantId) {
                            document.getElementById('variant_id').value = attr._variantId;
                            S.variantId = attr._variantId;
                        }

                        // 🔥 UPDATE DESCRIPTION HERE ALSO
                        if (attr._variantId) {
                            var selectedVariant = (window.__PRODUCT_VARIANTS__ || []).find(function(v) {
                                return v.id == attr._variantId;
                            });

                            if (selectedVariant) {
                                updateVariantDescription(selectedVariant.variant_description || '');
                            }
                        }


                        if (attr.image) setMainImage(attr.image);

                        if (attr.is_por) {
                            // ✅ FIX: Hide price, show POR input for POR attributes
                            hidePrice();
                            showPorInput();
                            document.getElementById('is_por').value = '1';
                            document.getElementById('por_price').value = '';
                        } else {
                            // ✅ FIX: Hide POR input, show and update price for non-POR
                            hidePorInput();
                            showPrice(); // ← CRITICAL FIX: Ensure price is shown
                            updatePrice(attr.mrp, attr.sell_price);
                            document.getElementById('is_por').value = '0';
                            document.getElementById('por_price').value = '';
                        }

                        // Update availability and Add to Cart button state
                        updateAvailabilityText(attr);
                        updateAddToCartButtonState(attr.id);
                    });

                    list.appendChild(btn);
                });

                // Auto-select first non-disabled attribute; if all out of stock, still show availability
                if (firstSelectable) {
                    firstSelectable.click();
                } else if (variant.attributes.length > 0) {
                    updateAvailabilityText(variant.attributes[0]);
                    updateAddToCartButtonState(variant.attributes[0].id);
                }
            }

            /* ================================================================
            ADD TO CART
            ================================================================ */
            function addToCartFinal() {
                var productId = document.getElementById('product_id').value;
                var variantId = document.getElementById('variant_id').value;
                var attrId = document.getElementById('variant_attribute_id').value;
                var qty = document.getElementById('qty').value;
                document.getElementById('pqty').value = qty;

                @if ($isVariant)
                    if (!variantId) {
                        alert('Please select a variant');
                        return;
                    }
                    if (document.getElementById('attributeBlock').style.display !== 'none' && !attrId) {
                        alert('Please select an option');
                        return;
                    }
                @endif

                if (!productId) {
                    alert('Product missing');
                    return;
                }
                if (qty <= 0) {
                    alert('Invalid quantity');
                    return;
                }

                // POR validation
                var isPor = document.getElementById('is_por').value === '1';
                if (isPor) {
                    var porPrice = document.getElementById('porPrice').value;
                    if (!porPrice || porPrice <= 0) {
                        alert('Please enter a valid price');
                        return;
                    }
                    document.getElementById('por_price').value = porPrice;
                }

                @if ($isVariant)
                    // Block add to cart if selected option is out of stock
                    if (attrId && isAttrOutOfStock(attrId)) {
                        alert('This option is out of stock. Please choose another.');
                        return;
                    }
                @endif

                // AJAX submit
                var form = document.getElementById('frmAddToCart');
                var formData = new FormData(form);

                $.ajax({
                    url: form.action,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        alert('Product ' + result.msg);

                        if (result.totalItem > 0) {
                            let totalPrice = 0;

                            $('.aa-cart-notify').text(result.totalItem);

                            var html = '<ul>';
                            $.each(result.data, function(i, item) {


                                var price = parseFloat(item.price);
                                var itemQty = parseInt(item.qty);
                                console.log(itemQty);
                                var lineTotal = itemQty * price;
                                console.log(lineTotal);
                                totalPrice += lineTotal;


                                html += '<li>' +
                                    '<a class="aa-cartbox-img" href="{{ url('/salesman/product') }}/' + item.slug +
                                    '"><img src="' + PRODUCT_IMAGE +
                                    '/' + item.image + '" alt="img"></a>' +
                                    '<div class="aa-cartbox-info">' +
                                    '<small><a href="{{ url('/salesman/product') }}/' + item.slug + '">' + item.product_title +
                                    '</a></small><br>' +
                                    (item.variant_name ? '<small>Variant: ' + item.variant_name +
                                        '</small><br>' : '') +
                                    (item.attribute_name ? '<small>Attribute: ' + item.attribute_name +
                                        '</small><br>' : '') +
                                    '<p>' + itemQty + ' × Rs ' + price + '</p>' +
                                    '</div>' +
                                    '</li>';
                            });
                            html +=
                                '<li><span class="aa-cartbox-total-title">Total</span><span class="aa-cartbox-total-price">Rs ' +
                                parseFloat(totalPrice).toFixed(2) + '</span></li>' +
                                '</ul>' +
                                '<a class="aa-cartbox-cart aa-primary-btn" href="{{ route('front.view.cart') }}">View Cart</a>';
                            document.querySelector('.aa-cartbox-summary').innerHTML = html;


                        }
                    },
                    error: function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg :
                            'Error adding to cart';
                        alert(msg);
                        console.log(xhr);
                    }
                });
            }

            function changeQty(delta) {
                var input = document.getElementById('qty');
                var val = parseInt(input.value) || 1;
                val += delta;
                if (val < 1) val = 1;
                if (val > 10) val = 10;
                input.value = val;
            }

            function qtyChanged() {
                var input = document.getElementById('qty');
                var val = parseInt(input.value);
                if (isNaN(val) || val < 1) input.value = 1;
                if (val > 10) input.value = 10;
            }

            function updateVariantDescription(text) {
                var block = document.getElementById('variantDescriptionBlock');
                var el = document.getElementById('variantDescriptionText');

                if (text && text.trim() !== '') {
                    el.innerText = text;
                    block.style.display = '';
                } else {
                    el.innerText = '';
                    block.style.display = 'none';
                }
            }


            /* ================================================================
            PAGE INIT
            ================================================================ */
            document.addEventListener('DOMContentLoaded', function() {

                // Wire add-to-cart button
                var cartBtn = document.getElementById('addToCartBtn');
                if (cartBtn) {
                    cartBtn.addEventListener('click', addToCartFinal);
                }

                @if ($isVariant)

                    // Boot the chain
                    if (window.__HAS_CODE_TYPES__) {
                        // Code types exist: populate variants for first code type
                        var firstCTBtn = document.querySelector('.codeTypeBtn.active');
                        if (firstCTBtn) selectCodeType(firstCTBtn);
                    } else {
                        // No code types: populate all variants
                        populateAllVariants();
                    }

                    // Wire code type buttons (event delegation)
                    document.addEventListener('click', function(e) {
                        if (e.target.classList.contains('codeTypeBtn')) {
                            e.preventDefault();
                            selectCodeType(e.target);
                        }
                    });
                @endif
            });
        </script>

    @endsection
