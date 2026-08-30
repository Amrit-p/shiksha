/* ==========================================================================
   Shiksha Lighting — enquiry.js
   Renders the enquiry list table and keeps the submission form in sync.
   Depends on catalog.js + main.js (window.SL).
   ========================================================================== */

(function (window, document) {
  "use strict";

  $(function () {
    if (!$("#enquiryRows").length || !window.SL) { return; }
    var SL = window.SL;
    var escapeHtml = SL.escapeHtml;

    /* ---------- Rows ---------- */
    function renderRows() {
      var items = SL.enquiry.items;

      $("#enquiryLayout").toggleClass("d-none", items.length === 0);
      $("#enquiryEmptyState").toggleClass("d-none", items.length > 0);
      $("#enquiryItemCount").text(items.length ? "(" + items.length + ")" : "");

      if (!items.length) { return; }

      $("#enquiryRows").html(items.map(function (item, idx) {
        var p = SL.byId(item.id);
        if (!p) { return ""; }
        return '<div class="cart-row" data-index="' + idx + '">' +
          '<img src="' + p.img + '" alt="' + escapeHtml(p.name) + '">' +
          '<div class="cr-info">' +
            '<div class="cr-title"><a href="products.html?id=' + p.id + '" class="text-ink">' + escapeHtml(p.name) + "</a></div>" +
            '<div class="cr-meta">' + escapeHtml(p.cat) +
              (p.series ? " · " + escapeHtml(SL.seriesLabel(p.series)) : "") + "</div>" +
            (p.code
              ? '<div class="cr-meta">Model code: <strong class="text-ink">' + escapeHtml(p.code) + "</strong></div>"
              : "") +
          "</div>" +
          '<div class="qty-stepper">' +
            '<button type="button" data-qty-minus aria-label="Decrease quantity"><i class="bi bi-dash-lg"></i></button>' +
            '<input type="number" value="' + item.qty + '" min="1" max="999" data-row-qty="' + idx + '" aria-label="Quantity for ' + escapeHtml(p.name) + '">' +
            '<button type="button" data-qty-plus aria-label="Increase quantity"><i class="bi bi-plus-lg"></i></button>' +
          "</div>" +
          '<button type="button" class="cart-remove" data-row-remove="' + idx + '" aria-label="Remove ' + escapeHtml(p.name) + '">' +
            '<i class="bi bi-trash3"></i></button>' +
          "</div>";
      }).join(""));
    }

    /* ---------- Form summary ---------- */
    function renderSummary() {
      var text = SL.enquiry.asText();
      $("#enqProducts").val(text || "No products selected yet.");
    }

    function render() { renderRows(); renderSummary(); }

    // main.js calls this whenever the enquiry list changes anywhere on the page.
    SL.renderEnquiryPage = render;

    /* ---------- Events ---------- */
    $("#enquiryRows").on("change", "[data-row-qty]", function () {
      SL.enquiry.setQty(parseInt($(this).data("row-qty"), 10), this.value);
    });

    $("#enquiryRows").on("click", "[data-row-remove]", function () {
      var idx = parseInt($(this).data("row-remove"), 10);
      $(this).closest(".cart-row").addClass("is-removing");
      window.setTimeout(function () {
        SL.enquiry.remove(idx);
        SL.toast("Removed from your enquiry.", "info");
      }, 320);
    });

    $("#clearEnquiry").on("click", function () {
      if (!SL.enquiry.items.length) { return; }
      SL.enquiry.clear();
      SL.toast("Enquiry list cleared.", "info");
    });

    render();
  });

})(window, document);
