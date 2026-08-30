/* ==========================================================================
   Shiksha Lighting — shop.js
   Category / series filtering, sorting, paging and URL sync.
   Depends on catalog.js + main.js (window.SL).
   ========================================================================== */

(function (window, document) {
  "use strict";

  var PER_PAGE = 24;

  $(function () {
    var $grid = $("#shopGrid");
    if (!$grid.length || !window.SL) { return; }

    var SL = window.SL;
    var ALL = SL.products;

    var state = {
      q: "",
      cats: [],
      series: [],
      sort: "default",
      view: "grid",
      page: 1
    };

    /* ---------- Category list with live counts ---------- */
    (function buildCats() {
      var counts = {};
      ALL.forEach(function (p) { counts[p.catKey] = (counts[p.catKey] || 0) + 1; });

      $("#catList").html(SL.categories.filter(function (c) {
        return counts[c.key];
      }).map(function (c) {
        return '<li><label class="filter-check">' +
          '<input type="checkbox" value="' + c.key + '">' +
          "<span>" + SL.escapeHtml(c.label) + "</span>" +
          '<span class="fc-count">' + counts[c.key] + "</span>" +
          "</label></li>";
      }).join(""));
    })();

    /* ---------- Lighting sub-range chips ---------- */
    (function buildSeries() {
      var counts = {};
      ALL.forEach(function (p) { if (p.series) { counts[p.series] = (counts[p.series] || 0) + 1; } });

      $("#seriesChips").html(SL.series.filter(function (s) {
        return counts[s.key];
      }).map(function (s) {
        return '<span class="chip" data-series="' + s.key + '">' +
          SL.escapeHtml(s.label) + ' <span class="opacity-75">' + counts[s.key] + "</span></span>";
      }).join(""));
    })();

    /* ---------- Initial state from the URL ---------- */
    (function fromUrl() {
      var params = new URLSearchParams(window.location.search);

      var cat = params.get("cat");
      if (cat) {
        state.cats = [cat];
        $('#catList input[value="' + cat + '"]').prop("checked", true);
      }
      var series = params.get("series");
      if (series) {
        state.series = [series];
        $('#seriesChips .chip[data-series="' + series + '"]').addClass("is-active");
      }
      var q = params.get("q");
      if (q) { state.q = q; $("#filterSearch").val(q); }

      var page = parseInt(params.get("page"), 10);
      if (page > 0) { state.page = page; }
    })();

    /* ---------- Filtering ---------- */
    function filtered() {
      var term = state.q.trim().toLowerCase();

      var list = ALL.filter(function (p) {
        if (term && (p.name + " " + p.cat + " " + p.code).toLowerCase().indexOf(term) === -1) { return false; }
        if (state.cats.length && state.cats.indexOf(p.catKey) === -1) { return false; }
        if (state.series.length && state.series.indexOf(p.series) === -1) { return false; }
        return true;
      });

      switch (state.sort) {
        case "name":      list.sort(function (a, b) { return a.name.localeCompare(b.name); }); break;
        case "name-desc": list.sort(function (a, b) { return b.name.localeCompare(a.name); }); break;
        case "category":  list.sort(function (a, b) { return a.cat.localeCompare(b.cat) || a.name.localeCompare(b.name); }); break;
        case "latest":    list.reverse(); break;
        default: break; // catalogue order
      }
      return list;
    }

    /* ---------- Active filter chips ---------- */
    function paintActiveChips() {
      var chips = [];

      state.cats.forEach(function (key) {
        chips.push({ label: SL.catLabel(key), type: "cat", value: key });
      });
      state.series.forEach(function (key) {
        chips.push({ label: SL.seriesLabel(key), type: "series", value: key });
      });
      if (state.q) { chips.push({ label: '“' + state.q + '”', type: "q", value: "" }); }

      $("#activeChips").html(chips.length
        ? chips.map(function (c) {
            return '<span class="chip is-active" data-remove-type="' + c.type + '" data-remove-value="' + c.value + '">' +
              SL.escapeHtml(c.label) + ' <i class="bi bi-x-lg ms-1 small"></i></span>';
          }).join("") +
          '<button type="button" class="chip" id="clearAllChips">Clear all</button>'
        : "");
    }

    /* ---------- Pagination ---------- */
    function paintPager(total) {
      var pages = Math.max(1, Math.ceil(total / PER_PAGE));
      if (state.page > pages) { state.page = pages; }

      if (pages <= 1) { $("#pager").empty(); return; }

      var html = "";
      html += '<a href="#shopTop" data-page="' + (state.page - 1) + '"' +
        (state.page === 1 ? ' class="is-disabled"' : "") +
        ' aria-label="Previous page"><i class="bi bi-chevron-left"></i></a>';

      var win = [];
      for (var i = 1; i <= pages; i++) {
        if (i === 1 || i === pages || Math.abs(i - state.page) <= 1) { win.push(i); }
      }

      var last = 0;
      win.forEach(function (i) {
        if (last && i - last > 1) { html += '<span class="is-gap">…</span>'; }
        html += i === state.page
          ? '<span class="is-current">' + i + "</span>"
          : '<a href="#shopTop" data-page="' + i + '">' + i + "</a>";
        last = i;
      });

      html += '<a href="#shopTop" data-page="' + (state.page + 1) + '"' +
        (state.page === pages ? ' class="is-disabled"' : "") +
        ' aria-label="Next page"><i class="bi bi-chevron-right"></i></a>';

      $("#pager").html(html);
    }

    /* ---------- Render ---------- */
    function render() {
      var list = filtered();
      var total = list.length;
      var pages = Math.max(1, Math.ceil(total / PER_PAGE));
      if (state.page > pages) { state.page = pages; }
      if (state.page < 1) { state.page = 1; }

      var start = (state.page - 1) * PER_PAGE;
      var slice = list.slice(start, start + PER_PAGE);

      $grid.html(slice.map(SL.cardHtml).join(""));

      $("#shownCount").text(total ? (start + 1) + "–" + (start + slice.length) : "0");
      $("#totalCount").text(total);
      $("#grandTotal").text(ALL.length);
      $("#noResults").toggle(total === 0);
      $grid.toggle(total > 0);

      // List view keeps col-12 and drops the wider steps
      $grid.children().toggleClass("col-sm-6 col-lg-4 col-xxl-4", state.view === "grid");
      $grid.find(".product-card").toggleClass("is-list", state.view === "list");

      paintActiveChips();
      paintPager(total);
      SL.saved.paint();
      SL.enquiry.paint();
      if (typeof SL.observeReveal === "function") { SL.observeReveal($grid); }
      else { $grid.find("[data-reveal]").addClass("is-revealed"); }
    }

    /* ---------- Shareable URL ---------- */
    function syncUrl() {
      var params = new URLSearchParams();
      if (state.cats.length === 1) { params.set("cat", state.cats[0]); }
      if (state.series.length === 1) { params.set("series", state.series[0]); }
      if (state.q) { params.set("q", state.q); }
      if (state.page > 1) { params.set("page", state.page); }
      var qs = params.toString();
      window.history.replaceState({}, "", qs ? "?" + qs : window.location.pathname);
    }

    function update(resetPage) {
      if (resetPage !== false) { state.page = 1; }
      render();
      syncUrl();
    }

    /* ---------- Events ---------- */
    var searchTimer = null;
    $("#filterSearch").on("input", function () {
      var value = this.value;
      window.clearTimeout(searchTimer);
      searchTimer = window.setTimeout(function () { state.q = value; update(); }, 220);
    });

    $("#catList").on("change", "input", function () {
      state.cats = $("#catList input:checked").map(function () { return this.value; }).get();
      update();
    });

    $("#clearCats").on("click", function () {
      $("#catList input").prop("checked", false);
      state.cats = [];
      update();
    });

    $("#seriesChips").on("click", ".chip", function () {
      var $c = $(this).toggleClass("is-active");
      var v = $c.data("series");
      var i = state.series.indexOf(v);
      if (i > -1) { state.series.splice(i, 1); } else { state.series.push(v); }
      update();
    });

    $("#sortBy").on("change", function () { state.sort = this.value; update(); });

    $(".view-toggle button").on("click", function () {
      $(".view-toggle button").removeClass("is-active");
      $(this).addClass("is-active");
      state.view = $(this).data("view");
      render();
    });

    $("#activeChips").on("click", ".chip", function () {
      if (this.id === "clearAllChips") { resetAll(); return; }
      var type = $(this).data("remove-type");
      var value = String($(this).data("remove-value"));

      if (type === "cat") {
        state.cats = state.cats.filter(function (c) { return c !== value; });
        $('#catList input[value="' + value + '"]').prop("checked", false);
      } else if (type === "series") {
        state.series = state.series.filter(function (s) { return s !== value; });
        $('#seriesChips .chip[data-series="' + value + '"]').removeClass("is-active");
      } else if (type === "q") {
        state.q = "";
        $("#filterSearch").val("");
      }
      update();
    });

    $("#pager").on("click", "a", function (e) {
      e.preventDefault();
      if ($(this).hasClass("is-disabled")) { return; }
      state.page = parseInt($(this).data("page"), 10) || 1;
      render();
      syncUrl();
      var top = $("#shopTop").offset().top - ($(".site-header").outerHeight() || 78) - 20;
      $("html, body").animate({ scrollTop: top }, 500);
    });

    function resetAll() {
      state.q = "";
      state.cats = [];
      state.series = [];
      state.sort = "default";
      state.page = 1;

      $("#filterSearch").val("");
      $("#catList input").prop("checked", false);
      $("#seriesChips .chip").removeClass("is-active");
      $("#sortBy").val("default");
      update();
      SL.toast("Filters cleared.", "info");
    }

    $("#resetFilters, #resetFilters2").on("click", resetAll);

    /* ---------- First paint ---------- */
    render();
    syncUrl();
  });

})(window, document);
