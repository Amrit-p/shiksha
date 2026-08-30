(function () {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  function cartUrls() {
    const body = document.body;
    return {
      add: body.dataset.shopCartAdd || '/cart/add',
      update: body.dataset.shopCartUpdate || '/cart/update',
      remove: body.dataset.shopCartRemove || '/cart/remove',
      clear: body.dataset.shopCartClear || '/cart/clear',
    };
  }

  function toast(message, isError, duration) {
    let el = document.querySelector('.shop-toast');
    if (!el) {
      el = document.createElement('div');
      el.className = 'toast shop-toast';
      document.body.appendChild(el);
    }
    el.textContent = message;
    el.style.background = isError ? '#b91c1c' : '#111';
    el.classList.add('show');
    clearTimeout(el._timer);
    el._timer = setTimeout(() => el.classList.remove('show'), duration || 2600);
  }

  // Surface server-side flash messages (session success/error) as a toast.
  // The inline banner used to render behind the fixed header and was invisible.
  function showFlash() {
    const flash = document.querySelector('[data-flash]');
    if (!flash) return;
    if (flash.dataset.flashSuccess) {
      toast(flash.dataset.flashSuccess, false, 5000);
    } else if (flash.dataset.flashError) {
      toast(flash.dataset.flashError, true, 5000);
    }
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', showFlash);
  } else {
    showFlash();
  }

  function updateCartBadge(count) {
    document.querySelectorAll('[data-cart-count]').forEach((badge) => {
      badge.textContent = String(count || 0);
      badge.hidden = Number(count) <= 0;
    });
  }

  // ---- Side cart drawer ----
  function miniCartUrl() {
    return document.body.dataset.shopCartMini || '/cart/mini';
  }
  function onCartPage() {
    return !!document.querySelector('[data-cart-page]');
  }
  function openSideCart() {
    const el = document.querySelector('[data-side-cart]');
    if (!el) return;
    el.classList.add('is-open');
    el.setAttribute('aria-hidden', 'false');
    document.querySelector('[data-side-cart-backdrop]')?.classList.add('is-open');
    document.body.classList.add('is-locked');
  }
  function closeSideCart() {
    const el = document.querySelector('[data-side-cart]');
    if (!el) return;
    el.classList.remove('is-open');
    el.setAttribute('aria-hidden', 'true');
    document.querySelector('[data-side-cart-backdrop]')?.classList.remove('is-open');
    document.body.classList.remove('is-locked');
  }
  async function refreshMiniCart() {
    const host = document.querySelector('[data-mini-cart]');
    if (!host) return;
    try {
      const res = await fetch(miniCartUrl(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
        credentials: 'same-origin',
      });
      const data = await res.json();
      if (typeof data.html === 'string') host.innerHTML = data.html;
      if (typeof data.count !== 'undefined') updateCartBadge(data.count);
    } catch (_) {
      /* keep whatever is currently shown */
    }
  }

  function setNavOpen(open) {
    document.body.classList.toggle('nav-open', open);
    document.querySelectorAll('[data-nav]').forEach((nav) => nav.classList.toggle('open', open));
    document.querySelectorAll('[data-nav-toggle]').forEach((btn) => {
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  async function postJson(url, payload) {
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-CSRF-TOKEN': csrf || '',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin',
      body: JSON.stringify(payload || {}),
    });

    let data = {};
    try {
      data = await res.json();
    } catch (_) {
      data = {};
    }

    if (!res.ok || data.success === false) {
      const msg =
        data.message ||
        (data.errors ? Object.values(data.errors).flat().join(' ') : null) ||
        'Something went wrong.';
      throw new Error(msg);
    }

    return data;
  }

  // Event delegation — only match action controls, never body URL datasets
  document.addEventListener('click', async (e) => {
    // Side cart open / close
    const openCartBtn = e.target.closest('[data-side-cart-open]');
    if (openCartBtn) {
      e.preventDefault();
      refreshMiniCart();
      openSideCart();
      return;
    }
    if (e.target.closest('[data-side-cart-close]') || e.target.closest('[data-side-cart-backdrop]')) {
      e.preventDefault();
      closeSideCart();
      return;
    }

    const addBtn = e.target.closest('button[data-add-to-cart], a[data-add-to-cart]');
    if (addBtn) {
      e.preventDefault();
      e.stopPropagation();
      const urls = cartUrls();
      const qtyInput = document.querySelector('[data-qty-input]');
      const payload = {
        product_id: Number(addBtn.dataset.productId),
        variant_id: addBtn.dataset.variantId ? Number(addBtn.dataset.variantId) : null,
        variant_attribute_id: addBtn.dataset.variantAttributeId
          ? Number(addBtn.dataset.variantAttributeId)
          : null,
        qty: Number(qtyInput?.value || addBtn.dataset.qty || 1),
      };

      const originalHtml = addBtn.innerHTML;
      try {
        addBtn.disabled = true;
        addBtn.classList.add('is-loading');
        addBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        const data = await postJson(addBtn.dataset.addToCart || urls.add, payload);
        updateCartBadge(data.count || 0);
        await refreshMiniCart();
        openSideCart();
      } catch (err) {
        toast(err.message || 'Unable to add item', true);
      } finally {
        addBtn.disabled = false;
        addBtn.classList.remove('is-loading');
        addBtn.innerHTML = originalHtml;
      }
      return;
    }

    const removeBtn = e.target.closest('button[data-cart-remove], a[data-cart-remove]');
    if (removeBtn) {
      e.preventDefault();
      try {
        const data = await postJson(cartUrls().remove, { key: removeBtn.dataset.cartRemove });
        updateCartBadge(data.count || 0);
        removeBtn.closest('[data-cart-line], tr')?.remove();
        toast(data.message || 'Removed');
        if ((data.count || 0) === 0 && onCartPage()) {
          window.location.reload();
        } else {
          refreshMiniCart();
        }
      } catch (err) {
        toast(err.message || 'Unable to remove item', true);
      }
      return;
    }

    const clearBtn = e.target.closest('button[data-cart-clear], a[data-cart-clear]');
    if (clearBtn) {
      e.preventDefault();
      try {
        await postJson(cartUrls().clear, {});
        window.location.reload();
      } catch (err) {
        toast(err.message || 'Unable to clear cart', true);
      }
      return;
    }

    const thumb = e.target.closest('img[data-thumb], [data-thumb]');
    if (thumb && thumb.matches('img, [data-thumb]')) {
      const main = document.querySelector('[data-gallery-main]');
      if (main && thumb.getAttribute('src')) {
        e.preventDefault();
        main.src = thumb.getAttribute('src');
        document.querySelectorAll('[data-thumb]').forEach((t) => t.classList.remove('active'));
        thumb.classList.add('active');
      }
      return;
    }

    const toggle = e.target.closest('[data-nav-toggle]');
    if (toggle) {
      e.preventDefault();
      setNavOpen(!document.body.classList.contains('nav-open'));
      return;
    }

    const filterToggle = e.target.closest('[data-filter-toggle]');
    if (filterToggle) {
      e.preventDefault();
      document.querySelector('[data-filters-panel]')?.classList.toggle('open');
      return;
    }

    if (e.target.closest('[data-nav-backdrop]')) {
      setNavOpen(false);
      return;
    }

    // Close mobile nav after choosing a link (do not prevent navigation)
    if (e.target.closest('[data-nav] a')) {
      setNavOpen(false);
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeSideCart();
  });

  document.addEventListener('change', async (e) => {
    const qtyInput = e.target.closest('input[data-cart-qty]');
    if (!qtyInput) return;

    try {
      const data = await postJson(cartUrls().update, {
        key: qtyInput.dataset.cartQty,
        qty: Number(qtyInput.value || 1),
      });
      updateCartBadge(data.count || 0);
      if (Number(qtyInput.value) < 1) qtyInput.closest('[data-cart-line], tr')?.remove();
      toast(data.message || 'Cart updated');
      if ((data.count || 0) === 0 && onCartPage()) {
        window.location.reload();
      } else {
        refreshMiniCart();
      }
    } catch (err) {
      toast(err.message || 'Unable to update cart', true);
    }
  });

  const filterForm = document.querySelector('[data-product-filters]');
  const gridHost = document.querySelector('[data-product-grid]');
  const paginationHost = document.querySelector('[data-product-pagination]');
  const countHost = document.querySelector('[data-product-count]');

  async function loadProducts(url) {
    if (!gridHost) return;
    gridHost.style.opacity = '0.55';
    try {
      const res = await fetch(url, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          Accept: 'application/json',
        },
        credentials: 'same-origin',
      });
      const data = await res.json();
      if (data.html) gridHost.innerHTML = data.html;
      if (paginationHost && data.pagination) paginationHost.innerHTML = data.pagination;
      if (countHost && typeof data.count !== 'undefined') countHost.textContent = data.count;
      history.replaceState({}, '', url);
      window.scrollTo({ top: gridHost.offsetTop - 80, behavior: 'smooth' });
    } catch (err) {
      // Fallback to full navigation if AJAX fails
      window.location.href = url;
    } finally {
      gridHost.style.opacity = '1';
    }
  }

  if (filterForm && gridHost) {
    filterForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const url = filterForm.action + '?' + new URLSearchParams(new FormData(filterForm)).toString();
      loadProducts(url);
      document.querySelector('[data-filters-panel]')?.classList.remove('open');
    });

    filterForm.querySelectorAll('select').forEach((select) => {
      select.addEventListener('change', () => {
        const url = filterForm.action + '?' + new URLSearchParams(new FormData(filterForm)).toString();
        loadProducts(url);
      });
    });
  }

  if (paginationHost) {
    paginationHost.addEventListener('click', (e) => {
      const link = e.target.closest('a');
      if (!link || !gridHost || !link.href) return;
      e.preventDefault();
      loadProducts(link.href);
    });
  }
})();
