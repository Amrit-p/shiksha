/* ============================================================================
   Cascading variation selector — customer product page.

   Steps, each rendered only when the product actually has that level:
     1. Product code type   (server-rendered pills)
     2. Variant name        (grouped: variants sharing a name become one pill)
     3. Custom fields       (one row per step, injected)
     4. Attribute option    (final row)

   Deliberately price-free: the customer site quotes on enquiry, so no MRP,
   sell price or POR handling lives here. Selecting an option only updates the
   image, availability and the ids carried by the "Add to enquiry cart" button.
   ========================================================================== */
(function () {
  'use strict';

  var selector = document.getElementById('pdVariantSelector');
  if (!selector) return;

  var VARIANTS = Array.isArray(window.__PRODUCT_VARIANTS__) ? window.__PRODUCT_VARIANTS__ : [];
  var DISABLED = Array.isArray(window.__DISABLED_ATTR_IDS__) ? window.__DISABLED_ATTR_IDS__ : [];

  var els = {
    codeTypeWrapper: document.getElementById('codeTypeWrapper'),
    variantWrapper: document.getElementById('variantWrapper'),
    cfSteps: document.getElementById('customFieldSteps'),
    attrBlock: document.getElementById('attributeBlock'),
    attrList: document.getElementById('attributeList'),
    noteBlock: document.getElementById('variantDescriptionBlock'),
    noteText: document.getElementById('variantDescriptionText'),
    skuBlock: document.getElementById('variantSkuBlock'),
    skuText: document.getElementById('variantSkuText'),
    availability: document.getElementById('pdAvailability'),
    addBtn: document.getElementById('pdAddToCart'),
    mainImage: document.querySelector('[data-gallery-main]')
  };

  var isProgrammaticSelection = false;
  var defaultMainImage = els.mainImage ? (els.mainImage.getAttribute('src') || '') : '';

  function triggerProgrammaticSelection(callback) {
    isProgrammaticSelection = true;
    try {
      callback();
    } finally {
      isProgrammaticSelection = false;
    }
  }

  // Current position in the cascade.
  var state = { variantId: null, group: null, attributeId: null };

  /* ------------------------------------------------------------------ utils */

  function setActive(wrapper, btn) {
    wrapper.querySelectorAll('button').forEach(function (b) { b.classList.remove('active'); });
    btn.classList.add('active');
  }

  function pill(text) {
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'opt-pill';
    btn.textContent = text;
    return btn;
  }

  function setMainImage(url) {
    if (!els.mainImage) return;
    if (isProgrammaticSelection) return;

    var cleanedUrl = (url || '').trim();
    if (!cleanedUrl || cleanedUrl === 'null' || cleanedUrl === 'undefined') {
      cleanedUrl = defaultMainImage;
    }

    els.mainImage.onerror = function () {
      if (els.mainImage.getAttribute('src') !== defaultMainImage) {
        els.mainImage.src = defaultMainImage;
      }
      els.mainImage.onerror = null;
    };

    els.mainImage.src = cleanedUrl;
    document.querySelectorAll('[data-thumb]').forEach(function (t) { t.classList.remove('active'); });
  }

  function isOutOfStock(attributeId) {
    if (attributeId === null || attributeId === '') return false;
    return DISABLED.some(function (id) { return String(id) === String(attributeId); });
  }

  /** Show the selected variant's reference, matching whatever is in state. */
  function syncSku() {
    if (!els.skuBlock) return;

    var variant = VARIANTS.find(function (v) { return String(v.id) === String(state.variantId); });
    var sku = variant && variant.sku ? String(variant.sku).trim() : '';

    els.skuText.textContent = sku;
    els.skuBlock.hidden = sku === '';
  }

  /** Push the current selection onto the add-to-cart button shop.js reads. */
  function syncAddToCart() {
    syncSku();
    if (!els.addBtn) return;

    if (state.variantId) els.addBtn.dataset.variantId = state.variantId;
    else delete els.addBtn.dataset.variantId;

    if (state.attributeId) els.addBtn.dataset.variantAttributeId = state.attributeId;
    else delete els.addBtn.dataset.variantAttributeId;

    var blocked = state.attributeId ? isOutOfStock(state.attributeId) : false;
    els.addBtn.disabled = blocked;
    els.addBtn.title = blocked ? 'This option is out of stock' : '';
  }

  function setAvailability(attr) {
    if (!els.availability) return;

    if (!attr) {
      els.availability.className = 'chip-static';
      els.availability.innerHTML = '<i class="bi bi-check-circle me-1"></i>Select an option';
      return;
    }

    var stock = parseInt(attr.stock, 10) || 0;
    if (stock > 0) {
      els.availability.className = 'chip-static';
      els.availability.innerHTML = '<i class="bi bi-check-circle me-1"></i>In stock';
    } else {
      els.availability.className = 'chip-static text-danger';
      els.availability.innerHTML = '<i class="bi bi-x-circle me-1"></i>Out of stock';
    }
  }

  function setNote(text) {
    if (!els.noteBlock) return;
    var value = (text || '').trim();
    els.noteText.textContent = value;
    els.noteBlock.hidden = value === '';
  }

  /** Heading for a custom-field step: its field type name when the step is uniform. */
  function stepHeading(items) {
    var names = items
      .map(function (item) { return (item.type_name || '').trim(); })
      .filter(function (name) { return name !== ''; });
    var unique = names.filter(function (name, i) { return names.indexOf(name) === i; });
    return unique.length === 1 ? 'Select ' + unique[0] : 'Select Option';
  }

  /* -------------------------------------------------------------- grouping */

  /**
   * Several DB variants can share one variant name (e.g. three "20 mm" rows,
   * each carrying a different option). They collapse into a single pill whose
   * attributes and custom fields are merged, each tagged with its source id.
   */
  function buildGroup(variants) {
    if (!variants || !variants.length) return null;

    var first = variants[0];
    var attributes = [];

    variants.forEach(function (variant) {
      (variant.attributes || []).forEach(function (attr) {
        attributes.push(Object.assign({}, attr, { _variantId: variant.id }));
      });
    });

    return {
      id: first.id,
      variant_name: first.variant_name,
      image: first.image,
      variant_description: first.variant_description || '',
      attributes: attributes,
      variants: variants
    };
  }

  function groupByName(variants) {
    var order = [];
    var groups = {};

    (variants || []).forEach(function (variant) {
      var key = variant.variantname_id
        ? 'id:' + variant.variantname_id
        : 'name:' + String(variant.variant_name || '').trim();

      if (!groups[key]) {
        groups[key] = [];
        order.push(key);
      }
      groups[key].push(variant);
    });

    return order.map(function (key) { return buildGroup(groups[key]); });
  }

  /* ---------------------------------------------- step 1: product code type */

  function selectCodeType(btn) {
    setActive(els.codeTypeWrapper, btn);
    renderVariants(VARIANTS.filter(function (variant) {
      return String(variant.product_code_type_id) === String(btn.dataset.codeTypeId);
    }));
  }

  /* ---------------------------------------------------- step 2: variant name */

  function renderVariants(variants) {
    els.variantWrapper.innerHTML = '';

    var first = null;
    groupByName(variants).forEach(function (group) {
      var btn = pill(group.variant_name);
      btn.addEventListener('click', function () {
        setActive(els.variantWrapper, btn);
        selectVariant(group);
      });
      els.variantWrapper.appendChild(btn);
      if (!first) first = btn;
    });

    if (first) triggerProgrammaticSelection(function () { first.click(); });
  }

  function selectVariant(group) {
    state.variantId = group.id;
    state.group = group;
    state.attributeId = null;

    setMainImage(group.image);
    setNote(group.variant_description);
    els.cfSteps.innerHTML = '';
    els.attrBlock.hidden = true;
    els.attrList.innerHTML = '';
    setAvailability(null);
    syncAddToCart();

    renderCustomFieldStep(0, customFieldSteps(group.variants));
  }

  /* ------------------------------------------------ steps 3…n: custom fields */

  /** Custom fields keyed by their position, each value tagged with its variant. */
  function customFieldSteps(variants) {
    var steps = {};

    variants.forEach(function (variant) {
      (variant.custom_fields || []).forEach(function (field, index) {
        if (!steps[index]) steps[index] = [];
        steps[index].push({
          value: field.value,
          type_name: field.type_name,
          variantId: variant.id
        });
      });
    });

    return steps;
  }

  function renderCustomFieldStep(stepIndex, steps) {
    Array.prototype.slice.call(els.cfSteps.children).forEach(function (row) {
      if (parseInt(row.dataset.step, 10) >= stepIndex) row.remove();
    });

    var items = steps[stepIndex] || [];

    // Step 0 chooses the concrete variant; later steps only offer that variant's values.
    if (stepIndex > 0) {
      items = items.filter(function (item) { return String(item.variantId) === String(state.variantId); });
    }

    var seen = {};
    items = items.filter(function (item) {
      var key = String(item.value || '').trim().toLowerCase();
      if (!key || seen[key]) return false;
      seen[key] = true;
      return true;
    });

    if (!items.length) {
      renderAttributes();
      return;
    }

    var row = document.createElement('div');
    row.className = 'selector-row';
    row.dataset.step = stepIndex;

    var label = document.createElement('span');
    label.className = 'selector-label';
    label.textContent = stepHeading(items);
    row.appendChild(label);

    var wrapper = document.createElement('div');
    wrapper.className = 'selector-options';

    items.forEach(function (item) {
      var btn = pill(item.value);
      btn.addEventListener('click', function () {
        setActive(wrapper, btn);

        if (stepIndex === 0) {
          state.variantId = item.variantId;
          var variant = VARIANTS.find(function (v) { return String(v.id) === String(item.variantId); });
          if (variant) {
            setNote(variant.variant_description);
            setMainImage(variant.image);
          }
          syncAddToCart();
        }

        renderCustomFieldStep(stepIndex + 1, steps);
      });
      wrapper.appendChild(btn);
    });

    row.appendChild(wrapper);
    els.cfSteps.appendChild(row);

    var firstBtn = wrapper.querySelector('button');
    if (firstBtn) triggerProgrammaticSelection(function () { firstBtn.click(); });
  }

  /* -------------------------------------------- final step: attribute option */

  function renderAttributes() {
    var group = state.group;
    if (!group) return;

    state.attributeId = null;
    els.attrList.innerHTML = '';

    // When custom fields split a name across several variants, only the
    // selected variant's options apply; otherwise show the merged set.
    var splitByCustomField = group.variants.length > 1 && group.variants.some(function (variant) {
      return (variant.custom_fields || []).length > 0;
    });

    var attributes = group.attributes;
    if (splitByCustomField) {
      var selected = VARIANTS.find(function (v) { return String(v.id) === String(state.variantId); });
      attributes = selected
        ? (selected.attributes || []).map(function (attr) {
            return Object.assign({}, attr, { _variantId: selected.id });
          })
        : [];
    }

    if (!attributes.length) {
      els.attrBlock.hidden = true;
      setAvailability(null);
      syncAddToCart();
      return;
    }

    els.attrBlock.hidden = false;

    var firstSelectable = null;

    attributes.forEach(function (attr) {
      var btn = pill(attr.label || 'Option');

      if (isOutOfStock(attr.id)) {
        btn.disabled = true;
        btn.title = 'Out of stock';
      } else if (!firstSelectable) {
        firstSelectable = btn;
      }

      btn.addEventListener('click', function () {
        setActive(els.attrList, btn);

        state.attributeId = attr.id;
        if (attr._variantId) state.variantId = attr._variantId;

        setMainImage(attr.image);
        setAvailability(attr);
        syncAddToCart();
      });

      els.attrList.appendChild(btn);
    });

    if (firstSelectable) {
      triggerProgrammaticSelection(function () { firstSelectable.click(); });
    } else {
      state.attributeId = attributes[0].id;
      setAvailability(attributes[0]);
      syncAddToCart();
    }
  }

  /* ------------------------------------------------------------------- boot */

  if (els.codeTypeWrapper) {
    els.codeTypeWrapper.addEventListener('click', function (e) {
      var btn = e.target.closest('.code-type-btn');
      if (btn) selectCodeType(btn);
    });

    var active = els.codeTypeWrapper.querySelector('.code-type-btn.active')
      || els.codeTypeWrapper.querySelector('.code-type-btn');
    if (active) selectCodeType(active);
  } else {
    renderVariants(VARIANTS);
  }
})();
