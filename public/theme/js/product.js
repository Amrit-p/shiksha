/* ==========================================================================
   Shiksha Lighting — product.js
   Fills the product detail page from ?id= and wires the enquiry actions.
   Depends on catalog.js + main.js (window.SL).
   ========================================================================== */

(function (window, document) {
  "use strict";

  $(function () {
    if (!$("#productDetail").length || !window.SL) { return; }
    var SL = window.SL;

    /* ---------- Resolve the product ---------- */
    var id = new URLSearchParams(window.location.search).get("id");
    var p = (id && SL.byId(id)) || SL.products[0];
    if (!p) { return; }

    document.title = p.name + " — Shiksha Lighting";

    /* ---------- Header / breadcrumb ---------- */
    $("[data-pd-hero-title]").text(p.name);
    $("[data-pd-crumb]").text(p.name);

    /* ---------- Core fields ---------- */
    $("[data-pd-cat]").text(p.cat);
    $("[data-pd-title]").text(p.name);
    $("[data-pd-code]").text(p.code || "—");
    $("[data-pd-sku]").text("SHK-" + p.sku);
    $("[data-pd-catlink]").text(p.cat).attr("href", "shop.html?cat=" + p.catKey);

    if (p.series) {
      $("[data-pd-series-wrap]").removeClass("d-none");
      $("[data-pd-series]").text(SL.seriesLabel(p.series))
        .attr("href", "shop.html?cat=lighting&series=" + p.series);
    } else {
      $("[data-pd-series-wrap]").addClass("d-none");
    }

    /* ---------- Image ---------- */
    $("[data-pd-img]").attr("src", p.img).attr("alt", p.name);

    // Hover magnifier
    var $main = $("#pdMain");
    if (!window.matchMedia("(hover: none)").matches) {
      $main.on("mouseenter", function () { $(this).addClass("is-zoom"); })
        .on("mouseleave", function () {
          $(this).removeClass("is-zoom");
          $(this).find("img").css("transform-origin", "center center");
        })
        .on("mousemove", function (e) {
          var rect = this.getBoundingClientRect();
          var x = ((e.clientX - rect.left) / rect.width) * 100;
          var y = ((e.clientY - rect.top) / rect.height) * 100;
          $(this).find("img").css("transform-origin", x + "% " + y + "%");
        });
    }

    /* ---------- Enquiry actions ---------- */
    $("[data-add-enquiry]").attr("data-add-enquiry", p.id);
    $("[data-save-toggle]").attr("data-save-toggle", p.id);
    SL.saved.paint();
    SL.enquiry.paint();

    $("[data-pd-enquire-now]").on("click", function (e) {
      e.preventDefault();
      SL.enquiry.add(p.id, parseInt($("#pdQty").val(), 10) || 1);
      window.setTimeout(function () { window.location.href = "enquiry.html"; }, 500);
    });

    // Prefill the "ask about this product" form
    $("[data-pd-question]").val("I would like more information about: " + p.name +
      (p.code ? " (" + p.code + ")" : "") + "\n\nPlease send specifications, availability and pricing.");

    /* ---------- Related products ---------- */
    var related = SL.products.filter(function (x) {
      return x.id !== p.id && x.catKey === p.catKey &&
        (!p.series || x.series === p.series);
    });

    // Widen to the whole category, then the whole catalogue, if thin
    if (related.length < 4) {
      SL.products.forEach(function (x) {
        if (related.length < 4 && x.id !== p.id && x.catKey === p.catKey && related.indexOf(x) === -1) {
          related.push(x);
        }
      });
    }
    if (related.length < 4) {
      SL.products.forEach(function (x) {
        if (related.length < 4 && x.id !== p.id && related.indexOf(x) === -1) { related.push(x); }
      });
    }

    $("#relatedGrid").html(related.slice(0, 4).map(SL.cardHtml).join(""));
    SL.saved.paint();
    SL.enquiry.paint();
    if (typeof SL.observeReveal === "function") { SL.observeReveal($("#relatedGrid")); }
  });

})(window, document);
