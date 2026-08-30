/* ==========================================================================
   Shiksha Lighting — main.js
   jQuery 3.7 + vanilla JS. Animation, navigation and the enquiry list.
   Catalogue data comes from catalog.js (window.SL_CATALOG) — load it first.
   ========================================================================== */

(function (window, document) {
  "use strict";

  var DATA = window.SL_CATALOG || { products: [], categories: [], series: [] };

  /* ======================================================================
     1. Namespace, helpers
     ====================================================================== */
  var SL = window.SL = {
    products: DATA.products,
    categories: DATA.categories,
    series: DATA.series,
    LIST_KEY: "shiksha_enquiry_v1",
    SAVED_KEY: "shiksha_saved_v1"
  };

  var reduceMotion = window.matchMedia &&
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  function byId(id) {
    for (var i = 0; i < SL.products.length; i++) {
      if (SL.products[i].id === id) { return SL.products[i]; }
    }
    return null;
  }
  SL.byId = byId;

  function catLabel(key) {
    for (var i = 0; i < SL.categories.length; i++) {
      if (SL.categories[i].key === key) { return SL.categories[i].label; }
    }
    return key;
  }
  SL.catLabel = catLabel;

  function seriesLabel(key) {
    for (var i = 0; i < SL.series.length; i++) {
      if (SL.series[i].key === key) { return SL.series[i].label; }
    }
    return key;
  }
  SL.seriesLabel = seriesLabel;

  function escapeHtml(s) {
    return String(s == null ? "" : s).replace(/[&<>"']/g, function (c) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
    });
  }
  SL.escapeHtml = escapeHtml;

  function readStore(key) {
    try {
      var raw = window.localStorage.getItem(key);
      return raw ? JSON.parse(raw) : [];
    } catch (e) { return []; }
  }

  function writeStore(key, value) {
    try { window.localStorage.setItem(key, JSON.stringify(value)); }
    catch (e) { /* storage unavailable — list stays in memory for this page */ }
  }

  /* ======================================================================
     2. Toast notifications
     ====================================================================== */
  function toast(message, type, icon) {
    var $wrap = $(".sl-toast-wrap");
    if (!$wrap.length) {
      $wrap = $('<div class="sl-toast-wrap" role="status" aria-live="polite"></div>').appendTo("body");
    }
    var icons = { ok: "bi-check-circle-fill", info: "bi-info-circle-fill", warn: "bi-exclamation-triangle-fill", err: "bi-x-circle-fill" };
    var $t = $(
      '<div class="sl-toast sl-toast--' + (type || "ok") + '">' +
      '<i class="bi ' + (icon || icons[type] || icons.ok) + '"></i>' +
      "<span>" + message + "</span></div>"
    ).appendTo($wrap);

    window.requestAnimationFrame(function () { $t.addClass("is-in"); });
    window.setTimeout(function () {
      $t.removeClass("is-in");
      window.setTimeout(function () { $t.remove(); }, 500);
    }, 2800);
  }
  SL.toast = toast;

  /* ======================================================================
     3. Preloader
     ====================================================================== */
  $(window).on("load", function () {
    var $pre = $("#preloader");
    if (!$pre.length) { return; }
    window.setTimeout(function () {
      $pre.addClass("is-done");
      $("body").removeClass("is-locked");
      window.setTimeout(function () { $pre.remove(); }, 700);
    }, 350);
  });

  // Safety net: never leave the preloader up if `load` is slow to fire.
  window.setTimeout(function () {
    $("#preloader").addClass("is-done");
    $("body").removeClass("is-locked");
  }, 4500);

  /* ======================================================================
     4. Header behaviour
     ====================================================================== */
  function initHeader() {
    var $header = $(".site-header");
    if (!$header.length) { return; }
    var lastY = 0;

    function onScroll() {
      var y = window.pageYOffset || document.documentElement.scrollTop;
      $header.toggleClass("is-stuck", y > 40);
      if (y > 320 && y > lastY && !$(".mobile-nav").hasClass("is-open")) {
        $header.addClass("is-hidden");
      } else {
        $header.removeClass("is-hidden");
      }
      lastY = y;

      var doc = document.documentElement;
      var max = doc.scrollHeight - doc.clientHeight;
      $("#scroll-progress").css("width", (max > 0 ? (y / max) * 100 : 0) + "%");
      $("#back-to-top").toggleClass("is-visible", y > 500);
    }

    $(window).on("scroll", onScroll);
    onScroll();

    $("#back-to-top").on("click", function () {
      $("html, body").animate({ scrollTop: 0 }, reduceMotion ? 0 : 700, "swing");
    });
  }

  /* ======================================================================
     5. Mobile navigation — slides in left → right
     ====================================================================== */
  function initMobileNav() {
    var $nav = $(".mobile-nav");
    var $backdrop = $(".mobile-nav-backdrop");
    var $burger = $(".hamburger");
    if (!$nav.length) { return; }

    function open() {
      $nav.addClass("is-open").attr("aria-hidden", "false");
      $backdrop.addClass("is-open");
      $burger.addClass("is-active").attr("aria-expanded", "true");
      $("body").addClass("is-locked");
    }

    function close() {
      $nav.removeClass("is-open").attr("aria-hidden", "true");
      $backdrop.removeClass("is-open");
      $burger.removeClass("is-active").attr("aria-expanded", "false");
      $("body").removeClass("is-locked");
    }

    SL.closeMobileNav = close;

    $burger.on("click", function () {
      if ($nav.hasClass("is-open")) { close(); } else { open(); }
    });
    $backdrop.on("click", close);
    $(".mobile-nav-close").on("click", close);

    $(".mobile-nav-toggle-sub").on("click", function (e) {
      e.preventDefault();
      $(this).toggleClass("is-open").next(".mobile-sub").slideToggle(260).toggleClass("is-open");
    });

    $nav.on("click", ".mobile-nav-link:not(.mobile-nav-toggle-sub), .mobile-sub a", function () {
      window.setTimeout(close, 160);
    });

    $(document).on("keydown", function (e) {
      if (e.key === "Escape" && $nav.hasClass("is-open")) { close(); }
    });

    $(window).on("resize", function () {
      if (window.innerWidth >= 992 && $nav.hasClass("is-open")) { close(); }
    });
  }

  /* ======================================================================
     6. Search overlay
     ====================================================================== */
  function initSearch() {
    var $overlay = $(".search-overlay");
    if (!$overlay.length) { return; }
    var $input = $overlay.find("input");
    var $results = $overlay.find(".search-results");

    function open() {
      $overlay.addClass("is-open").attr("aria-hidden", "false");
      $("body").addClass("is-locked");
      window.setTimeout(function () { $input.trigger("focus"); }, 320);
    }
    function close() {
      $overlay.removeClass("is-open").attr("aria-hidden", "true");
      $("body").removeClass("is-locked");
    }

    $("[data-search-open]").on("click", function (e) { e.preventDefault(); open(); });
    $(".search-overlay").on("click", function (e) { if (e.target === this) { close(); } });
    $(".search-close").on("click", close);
    $(document).on("keydown", function (e) {
      if (e.key === "Escape" && $overlay.hasClass("is-open")) { close(); }
    });

    // Live suggestions from the Laravel backend. The input lives inside a real
    // <form> that GETs the search route, so pressing Enter always falls back to
    // the full results page; typing shows clickable product suggestions.
    var suggestUrl = $overlay.attr("data-suggest-url");
    var debounce = null;
    var lastTerm = null;

    function hint(msg) {
      $results.html('<p class="text-center text-white-50 mb-0 py-3 small">' + msg + "</p>");
    }

    function renderResults(term, data) {
      var list = (data && data.results) || [];
      if (!list.length) {
        hint("No products match “" + escapeHtml(term) + "”. Press Enter to search anyway.");
        return;
      }
      var html = list.map(function (p) {
        return '<a class="search-result" href="' + p.url + '">' +
          '<img src="' + p.image + '" alt="' + escapeHtml(p.title) + '">' +
          '<span><span class="d-block fw-semibold">' + escapeHtml(p.title) + "</span>" +
          '<small class="text-white-50">' + escapeHtml(p.category) + "</small></span>" +
          '<span class="sr-price">View <i class="bi bi-arrow-right"></i></span></a>';
      }).join("");
      if (data.more && data.allUrl) {
        html += '<a class="search-result justify-content-center" href="' + data.allUrl + '">' +
          '<span class="fw-semibold">See all ' + data.total + " results <i class=\"bi bi-arrow-right\"></i></span></a>";
      }
      $results.html(html);
    }

    function fetchSuggest(term) {
      if (!suggestUrl) { return; }
      $.ajax({
        url: suggestUrl,
        data: { q: term },
        dataType: "json",
        headers: { "X-Requested-With": "XMLHttpRequest" }
      }).done(function (data) {
        if ($.trim($input.val()) === term) { renderResults(term, data); }
      }).fail(function () {
        hint("Press Enter to search the catalogue.");
      });
    }

    $input.on("input", function () {
      var term = $.trim(this.value);
      if (term === lastTerm) { return; }
      lastTerm = term;
      window.clearTimeout(debounce);
      if (term.length < 2) {
        hint("Type a product name to see suggestions.");
        return;
      }
      hint("Searching…");
      debounce = window.setTimeout(function () { fetchSuggest(term); }, 250);
    });

    $(".search-tag").on("click", function (e) {
      e.preventDefault();
      $input.val($(this).text()).trigger("input").trigger("focus");
    });
  }

  /* ======================================================================
     7. Enquiry list  (replaces a priced shopping cart)
     ====================================================================== */
  var Enquiry = SL.enquiry = {
    items: readStore(SL.LIST_KEY),

    save: function () {
      writeStore(SL.LIST_KEY, this.items);
      this.paint();
    },

    has: function (id) {
      for (var i = 0; i < this.items.length; i++) {
        if (this.items[i].id === id) { return true; }
      }
      return false;
    },

    add: function (id, qty) {
      var p = byId(id);
      if (!p) { return; }
      qty = Math.max(1, parseInt(qty, 10) || 1);

      var existing = null;
      for (var i = 0; i < this.items.length; i++) {
        if (this.items[i].id === id) { existing = this.items[i]; break; }
      }
      if (existing) {
        existing.qty += qty;
        toast("Quantity updated for <strong>" + escapeHtml(p.name) + "</strong>.", "info");
      } else {
        this.items.push({ id: id, qty: qty });
        toast("<strong>" + escapeHtml(p.name) + "</strong> added to your enquiry.", "ok");
      }
      this.save();

      $(".count-badge[data-list-count]").addClass("is-bump");
      window.setTimeout(function () { $(".count-badge[data-list-count]").removeClass("is-bump"); }, 520);
    },

    setQty: function (index, qty) {
      if (!this.items[index]) { return; }
      qty = parseInt(qty, 10);
      if (isNaN(qty) || qty < 1) { qty = 1; }
      this.items[index].qty = Math.min(qty, 999);
      this.save();
    },

    remove: function (index) {
      this.items.splice(index, 1);
      this.save();
    },

    clear: function () { this.items = []; this.save(); },

    count: function () { return this.items.length; },

    units: function () {
      return this.items.reduce(function (t, i) { return t + i.qty; }, 0);
    },

    /* Plain-text summary dropped into the enquiry form */
    asText: function () {
      return this.items.map(function (i, n) {
        var p = byId(i.id);
        if (!p) { return ""; }
        return (n + 1) + ". " + p.name + (p.code ? " [" + p.code + "]" : "") +
          " — " + p.cat + " — Qty: " + i.qty;
      }).filter(Boolean).join("\n");
    },

    paint: function () {
      var n = this.count();
      $("[data-list-count]").text(n).toggleClass("d-none", n === 0);
      $("[data-add-enquiry]").each(function () {
        var $b = $(this);
        $b.toggleClass("is-in-list", Enquiry.has($b.attr("data-add-enquiry")));
      });
      this.paintDrawer();
      if (typeof SL.renderEnquiryPage === "function") { SL.renderEnquiryPage(); }
    },

    paintDrawer: function () {
      var $body = $(".cart-drawer-body");
      if (!$body.length) { return; }
      var self = this;

      if (!this.items.length) {
        $body.html(
          '<div class="cart-empty"><i class="bi bi-clipboard-x"></i>' +
          '<h6 class="mb-1">Your enquiry list is empty</h6>' +
          '<p class="small text-muted-2 mb-3">Add the products you need and send them to us in one go.</p>' +
          '<a href="shop.html" class="btn btn-brand btn-sm">Browse products</a></div>'
        );
      } else {
        $body.html(this.items.map(function (item, idx) {
          var p = byId(item.id);
          if (!p) { return ""; }
          return '<div class="mini-item" style="animation-delay:' + (idx * 50) + 'ms">' +
            '<img src="' + p.img + '" alt="' + escapeHtml(p.name) + '">' +
            '<div class="flex-grow-1">' +
              '<h6 class="mb-1"><a href="products.html?id=' + p.id + '" class="text-ink">' + escapeHtml(p.name) + "</a></h6>" +
              '<div class="mi-meta mb-1">' + escapeHtml(p.cat) + "</div>" +
              '<div class="mi-price">Qty: ' + item.qty + "</div>" +
            "</div>" +
            '<button class="mini-remove" data-mini-remove="' + idx + '" aria-label="Remove ' + escapeHtml(p.name) + '"><i class="bi bi-x-lg"></i></button>' +
            "</div>";
        }).join(""));
      }

      $(".enquiry-line-count").text(this.count() + (this.count() === 1 ? " product" : " products"));
      $(".enquiry-unit-count").text(this.units());
      $(".cart-drawer-foot").toggleClass("d-none", this.items.length === 0);

      $body.off("click.mini").on("click.mini", "[data-mini-remove]", function () {
        self.remove(parseInt($(this).data("mini-remove"), 10));
        toast("Removed from enquiry list.", "info");
      });
    }
  };

  function initEnquiryDrawer() {
    var $drawer = $(".cart-drawer");
    if (!$drawer.length) { return; }
    var $backdrop = $(".mobile-nav-backdrop");

    $("[data-list-open]").on("click", function (e) {
      e.preventDefault();
      $drawer.addClass("is-open").attr("aria-hidden", "false");
      $backdrop.addClass("is-open");
      $("body").addClass("is-locked");
    });

    function close() {
      $drawer.removeClass("is-open").attr("aria-hidden", "true");
      if (!$(".mobile-nav").hasClass("is-open")) {
        $backdrop.removeClass("is-open");
        $("body").removeClass("is-locked");
      }
    }
    $("[data-list-close]").on("click", close);
    $backdrop.on("click", close);
    $(document).on("keydown", function (e) {
      if (e.key === "Escape" && $drawer.hasClass("is-open")) { close(); }
    });
  }

  /* ======================================================================
     8. Saved / shortlist
     ====================================================================== */
  var Saved = SL.saved = {
    items: readStore(SL.SAVED_KEY),
    has: function (id) { return this.items.indexOf(id) > -1; },
    toggle: function (id) {
      var i = this.items.indexOf(id);
      if (i > -1) { this.items.splice(i, 1); } else { this.items.push(id); }
      writeStore(SL.SAVED_KEY, this.items);
      this.paint();
      return i === -1;
    },
    paint: function () {
      var n = this.items.length;
      $("[data-saved-count]").text(n).toggleClass("d-none", n === 0);
      var self = this;
      $("[data-save-toggle]").each(function () {
        $(this).toggleClass("is-active", self.has($(this).attr("data-save-toggle")));
      });
    }
  };

  /* ======================================================================
     9. Product card rendering
     ====================================================================== */
  function cardHtml(p, index) {
    var badge = "";
    if (p.code) {
      badge = '<span class="p-badge p-badge--code">' + escapeHtml(p.code) + "</span>";
    }
    var seriesTag = p.series
      ? '<span class="product-series">' + escapeHtml(seriesLabel(p.series)) + "</span>"
      : "";

    return '' +
      '<div class="col-12 col-sm-6 col-lg-3 col-xxl-3" data-reveal="rise" style="--reveal-delay:' + ((index % 4) * 80) + 'ms">' +
        '<article class="product-card" data-tilt data-tilt-max="5" data-product="' + p.id + '">' +
          '<div class="product-badges">' + badge + "</div>" +
          '<div class="product-actions">' +
            '<button type="button" class="pa-btn" data-save-toggle="' + p.id + '" aria-label="Save to shortlist"><i class="bi bi-heart"></i></button>' +
            '<button type="button" class="pa-btn" data-quickview="' + p.id + '" aria-label="Quick view"><i class="bi bi-eye"></i></button>' +
          "</div>" +
          '<a class="product-media" href="products.html?id=' + p.id + '">' +
            '<img src="' + p.img + '" alt="' + escapeHtml(p.name) + '" loading="lazy" width="600" height="600">' +
          "</a>" +
          '<div class="product-body">' +
            '<div class="product-cat">' + escapeHtml(p.cat) + "</div>" +
            '<h3 class="product-title"><a href="products.html?id=' + p.id + '">' + escapeHtml(p.name) + "</a></h3>" +
            seriesTag +
            '<div class="product-foot">' +
              '<span class="product-enquire"><i class="bi bi-tag me-1"></i>Enquire for price</span>' +
              '<button type="button" class="btn-cart-mini" data-add-enquiry="' + p.id + '" aria-label="Add ' + escapeHtml(p.name) + ' to enquiry">' +
                '<i class="bi bi-plus-lg"></i></button>' +
            "</div>" +
          "</div>" +
        "</article>" +
      "</div>";
  }
  SL.cardHtml = cardHtml;

  function renderGrids() {
    // The Laravel app renders products server-side into [data-product-grid] and
    // manages them via shop.js. The static catalogue is not loaded here, so bail
    // out rather than overwriting real products with an empty list.
    if (!SL.products.length) { return; }
    $("[data-product-grid]").each(function () {
      var $grid = $(this);
      var mode = $grid.data("product-grid");
      var limit = parseInt($grid.data("limit"), 10) || 12;
      var exclude = $grid.data("exclude");
      var catKey = $grid.data("cat");
      var seriesKey = $grid.data("series");

      var list = SL.products.slice();
      if (catKey) { list = list.filter(function (p) { return p.catKey === catKey; }); }
      if (seriesKey) { list = list.filter(function (p) { return p.series === seriesKey; }); }
      if (exclude) { list = list.filter(function (p) { return p.id !== exclude; }); }
      if (mode === "latest") { list.reverse(); }

      $grid.html(list.slice(0, limit).map(cardHtml).join(""));
    });
  }

  /* ======================================================================
     10. Card interactions (delegated)
     ====================================================================== */
  function initProductInteractions() {
    $(document).on("click", "[data-add-enquiry]", function (e) {
      e.preventDefault();
      var $btn = $(this);
      var qty = $btn.data("qty-from") ? $($btn.data("qty-from")).val() : 1;
      Enquiry.add($btn.attr("data-add-enquiry"), qty);

      var original = $btn.html();
      if ($btn.hasClass("btn-cart-mini")) {
        $btn.html('<i class="bi bi-check-lg"></i>');
        window.setTimeout(function () { $btn.html(original); }, 1400);
      }
    });

    $(document).on("click", "[data-save-toggle]", function (e) {
      e.preventDefault();
      var id = $(this).attr("data-save-toggle");
      var added = Saved.toggle(id);
      var p = byId(id);
      toast(added
        ? "<strong>" + escapeHtml(p.name) + "</strong> saved to your shortlist."
        : "Removed from shortlist.", added ? "ok" : "info", added ? "bi-heart-fill" : "bi-heart");
    });

    $(document).on("click", "[data-quickview]", function (e) {
      e.preventDefault();
      var p = byId($(this).attr("data-quickview"));
      if (!p) { return; }
      var $modal = $("#quickViewModal");
      if (!$modal.length) { window.location.href = "products.html?id=" + p.id; return; }

      $modal.find("[data-qv-img]").attr("src", p.img).attr("alt", p.name);
      $modal.find("[data-qv-cat]").text(p.cat);
      $modal.find("[data-qv-title]").text(p.name);
      $modal.find("[data-qv-code]").text(p.code || "—");
      $modal.find("[data-qv-series]").text(p.series ? seriesLabel(p.series) : p.cat);
      $modal.find("[data-add-enquiry]").attr("data-add-enquiry", p.id);
      $modal.find("[data-qv-link]").attr("href", "products.html?id=" + p.id);

      if (window.bootstrap) { bootstrap.Modal.getOrCreateInstance($modal[0]).show(); }
    });
  }

  /* ======================================================================
     11. Scroll reveal
     ====================================================================== */
  function initReveal() {
    var nodes = document.querySelectorAll("[data-reveal], .reveal-lines");
    if (!nodes.length) { return; }

    if (reduceMotion || !("IntersectionObserver" in window)) {
      Array.prototype.forEach.call(nodes, function (n) { n.classList.add("is-revealed"); });
      SL.observeReveal = function (root) {
        $(root).find("[data-reveal], .reveal-lines").addClass("is-revealed");
      };
      return;
    }

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-revealed");
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.08, rootMargin: "0px 0px -50px 0px" });

    Array.prototype.forEach.call(nodes, function (n) { io.observe(n); });
    SL.observeReveal = function (root) {
      $(root).find("[data-reveal], .reveal-lines").each(function () { io.observe(this); });
    };
  }

  /* ======================================================================
     12. Counters
     ====================================================================== */
  function initCounters() {
    var nodes = document.querySelectorAll("[data-count]");
    if (!nodes.length) { return; }

    function run(el) {
      var target = parseFloat(el.getAttribute("data-count"));
      var decimals = parseInt(el.getAttribute("data-decimals"), 10) || 0;
      var duration = parseInt(el.getAttribute("data-duration"), 10) || 1800;
      if (reduceMotion) { el.textContent = target.toFixed(decimals); return; }
      var start = null;

      function tick(ts) {
        if (!start) { start = ts; }
        var progress = Math.min((ts - start) / duration, 1);
        var eased = 1 - Math.pow(1 - progress, 3);
        var value = target * eased;
        el.textContent = decimals ? value.toFixed(decimals) : Math.floor(value).toLocaleString("en-IN");
        if (progress < 1) { window.requestAnimationFrame(tick); }
        else { el.textContent = decimals ? target.toFixed(decimals) : target.toLocaleString("en-IN"); }
      }
      window.requestAnimationFrame(tick);
    }

    if (!("IntersectionObserver" in window)) {
      Array.prototype.forEach.call(nodes, run);
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) { run(entry.target); io.unobserve(entry.target); }
      });
    }, { threshold: 0.5 });
    Array.prototype.forEach.call(nodes, function (n) { io.observe(n); });
  }

  /* ======================================================================
     13. Parallax + tilt + cursor + ripple
     ====================================================================== */
  function initParallax() {
    var nodes = document.querySelectorAll("[data-parallax]");
    if (!nodes.length || reduceMotion) { return; }
    var ticking = false;

    function update() {
      var vh = window.innerHeight;
      Array.prototype.forEach.call(nodes, function (el) {
        var speed = parseFloat(el.getAttribute("data-parallax")) || 0.15;
        var rect = el.getBoundingClientRect();
        if (rect.bottom < -200 || rect.top > vh + 200) { return; }
        var offset = (rect.top + rect.height / 2 - vh / 2) * speed * -1;
        el.style.transform = "translate3d(0," + offset.toFixed(1) + "px,0)";
      });
      ticking = false;
    }

    $(window).on("scroll resize", function () {
      if (!ticking) { window.requestAnimationFrame(update); ticking = true; }
    });
    update();
  }

  function initTilt() {
    if (reduceMotion || window.matchMedia("(hover: none)").matches) { return; }

    $(document).on("mousemove", "[data-tilt]", function (e) {
      var el = this;
      var rect = el.getBoundingClientRect();
      var max = parseFloat(el.getAttribute("data-tilt-max")) || 7;
      var px = (e.clientX - rect.left) / rect.width;
      var py = (e.clientY - rect.top) / rect.height;
      el.style.setProperty("--ry", ((px - 0.5) * max * 2).toFixed(2) + "deg");
      el.style.setProperty("--rx", ((0.5 - py) * max * 2).toFixed(2) + "deg");
      el.classList.add("is-tilting");
    });

    $(document).on("mouseleave", "[data-tilt]", function () {
      this.classList.remove("is-tilting");
      this.style.setProperty("--ry", "0deg");
      this.style.setProperty("--rx", "0deg");
    });
  }

  function initCursorGlow() {
    if (reduceMotion || window.matchMedia("(hover: none)").matches) { return; }
    var el = document.getElementById("cursor-glow");
    if (!el) { return; }
    var x = 0, y = 0, cx = 0, cy = 0, running = false;

    $(document).on("mousemove", function (e) {
      x = e.clientX; y = e.clientY;
      el.classList.add("is-on");
      if (!running) { running = true; window.requestAnimationFrame(loop); }
    });

    function loop() {
      cx += (x - cx) * 0.12;
      cy += (y - cy) * 0.12;
      el.style.transform = "translate3d(" + cx.toFixed(1) + "px," + cy.toFixed(1) + "px,0)";
      if (Math.abs(x - cx) > 0.4 || Math.abs(y - cy) > 0.4) { window.requestAnimationFrame(loop); }
      else { running = false; }
    }

    $(document).on("mouseleave", function () { el.classList.remove("is-on"); });
  }

  function initRipple() {
    if (reduceMotion) { return; }
    $(document).on("click", ".btn", function (e) {
      var $btn = $(this);
      var rect = this.getBoundingClientRect();
      var size = Math.max(rect.width, rect.height);
      $('<span class="ripple-ink"></span>').css({
        width: size, height: size,
        left: e.clientX - rect.left - size / 2,
        top: e.clientY - rect.top - size / 2
      }).appendTo($btn).on("animationend", function () { $(this).remove(); });
    });
  }

  function initMagnetic() {
    if (reduceMotion || window.matchMedia("(hover: none)").matches) { return; }
    $("[data-magnetic]").on("mousemove", function (e) {
      var rect = this.getBoundingClientRect();
      var strength = parseFloat($(this).data("magnetic")) || 0.28;
      var dx = (e.clientX - rect.left - rect.width / 2) * strength;
      var dy = (e.clientY - rect.top - rect.height / 2) * strength;
      $(this).css("transform", "translate(" + dx + "px," + dy + "px)");
    }).on("mouseleave", function () {
      $(this).css("transform", "");
    });
  }

  /* ======================================================================
     14. Smooth anchor scrolling
     ====================================================================== */
  function initSmoothScroll() {
    $(document).on("click", 'a[href^="#"]:not([href="#"]):not([data-bs-toggle])', function (e) {
      var hash = this.getAttribute("href");
      var $target = $(hash);
      if (!$target.length) { return; }
      e.preventDefault();
      var offset = $(".site-header").outerHeight() || 78;
      $("html, body").animate(
        { scrollTop: $target.offset().top - offset - 16 },
        reduceMotion ? 0 : 800,
        "swing"
      );
    });
  }

  /* ======================================================================
     15. Hero typed headline
     ====================================================================== */
  function initTyped() {
    var $el = $("[data-typed]");
    if (!$el.length) { return; }
    var words = String($el.data("typed")).split("|");
    if (reduceMotion) { $el.text(words[0]); return; }

    var wi = 0, ci = 0, deleting = false;

    function step() {
      var word = words[wi];
      $el.text(deleting ? word.substring(0, ci--) : word.substring(0, ci++));
      var delay = deleting ? 45 : 95;

      if (!deleting && ci > word.length) { deleting = true; delay = 1500; }
      else if (deleting && ci < 0) {
        deleting = false;
        wi = (wi + 1) % words.length;
        ci = 0;
        delay = 260;
      }
      window.setTimeout(step, delay);
    }
    step();
  }

  /* ======================================================================
     16. Slider (generic horizontal track)
     ====================================================================== */
  function initSlider() {
    var $slider = $("[data-slider]");
    if (!$slider.length) { return; }
    var $track = $slider.find(".testi-track");
    var $slides = $track.children();
    var index = 0;
    var timer = null;

    function perView() {
      if (window.innerWidth >= 992) { return 3; }
      if (window.innerWidth >= 768) { return 2; }
      return 1;
    }
    function maxIndex() { return Math.max(0, $slides.length - perView()); }

    function go(i) {
      index = Math.min(Math.max(i, 0), maxIndex());
      $track.css("transform", "translateX(-" + (index * (100 / perView())) + "%)");
      $slider.find(".slider-dot").each(function (d) {
        $(this).toggleClass("is-active", d === index);
      });
    }

    function buildDots() {
      var $dots = $slider.find(".slider-dots");
      if (!$dots.length) { return; }
      var html = "";
      for (var i = 0; i <= maxIndex(); i++) {
        html += '<button type="button" class="slider-dot" data-go="' + i + '" aria-label="Go to slide ' + (i + 1) + '"></button>';
      }
      $dots.html(html);
    }

    function autoplay() {
      if (reduceMotion) { return; }
      window.clearInterval(timer);
      timer = window.setInterval(function () {
        go(index >= maxIndex() ? 0 : index + 1);
      }, 5200);
    }

    $slider.on("click", "[data-slide-prev]", function () { go(index - 1); autoplay(); });
    $slider.on("click", "[data-slide-next]", function () { go(index >= maxIndex() ? 0 : index + 1); autoplay(); });
    $slider.on("click", ".slider-dot", function () { go(parseInt($(this).data("go"), 10)); autoplay(); });
    $slider.on("mouseenter", function () { window.clearInterval(timer); });
    $slider.on("mouseleave", autoplay);
    $(window).on("resize", function () { buildDots(); go(index); });

    buildDots();
    go(0);
    autoplay();
  }

  /* ======================================================================
     17. Forms
     ====================================================================== */
  function initForms() {
    $("[data-validate]").on("submit", function (e) {
      e.preventDefault();
      var form = this;
      var $form = $(form);
      $form.addClass("was-validated");

      if (!form.checkValidity()) {
        var $first = $form.find(":invalid").first();
        if ($first.length) {
          $("html, body").animate({ scrollTop: $first.offset().top - 160 }, 400);
          $first.trigger("focus");
        }
        toast("Please complete the highlighted fields.", "warn");
        return;
      }

      var $btn = $form.find('[type="submit"]');
      var label = $btn.html();
      $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm me-2"></span>Sending…');

      // No backend in this build — simulate the round trip.
      window.setTimeout(function () {
        $btn.prop("disabled", false).html(label);
        form.reset();
        $form.removeClass("was-validated");
        toast($form.data("success") || "Thank you! We will get back to you shortly.", "ok");
        if ($form.is("[data-clears-enquiry]")) { Enquiry.clear(); }
      }, 1200);
    });

    $("[data-newsletter]").on("submit", function (e) {
      e.preventDefault();
      var $input = $(this).find('input[type="email"]');
      var value = $.trim($input.val());
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value)) {
        toast("Please enter a valid email address.", "warn");
        $input.trigger("focus");
        return;
      }
      $input.val("");
      toast("You are subscribed — thank you!", "ok");
    });
  }

  /* ======================================================================
     18. Quantity steppers
     ====================================================================== */
  function initSteppers() {
    $(document).on("click", "[data-qty-minus], [data-qty-plus]", function () {
      var $input = $(this).closest(".qty-stepper").find("input");
      var value = parseInt($input.val(), 10) || 1;
      value += $(this).is("[data-qty-plus]") ? 1 : -1;
      $input.val(Math.min(999, Math.max(1, value))).trigger("change");
    });
  }

  /* ======================================================================
     19. Boot
     ====================================================================== */
  $(function () {
    renderGrids();

    initHeader();
    initMobileNav();
    initSearch();
    initEnquiryDrawer();
    initProductInteractions();
    initReveal();
    initCounters();
    initParallax();
    initTilt();
    initCursorGlow();
    initRipple();
    initMagnetic();
    initSmoothScroll();
    initTyped();
    initSlider();
    initForms();
    initSteppers();

    Enquiry.paint();
    Saved.paint();

    // Mark the current page in both navigations
    var page = window.location.pathname.split("/").pop() || "index.html";
    $(".main-nav .nav-link, .mobile-nav-link").each(function () {
      var href = ($(this).attr("href") || "").split("?")[0];
      if (href && href === page) { $(this).addClass("active"); }
    });
  });

})(window, document);
