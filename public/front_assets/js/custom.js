/** 
  * Template Name: Daily Shop
  * Version: 1.0  
  * Template Scripts
  * Author: MarkUps
  * Author URI: http://www.markups.io/

  Custom JS
  

  1. CARTBOX
  2. TOOLTIP
  3. PRODUCT VIEW SLIDER 
  4. POPULAR PRODUCT SLIDER (SLICK SLIDER) 
  5. FEATURED PRODUCT SLIDER (SLICK SLIDER)
  6. LATEST PRODUCT SLIDER (SLICK SLIDER) 
  7. TESTIMONIAL SLIDER (SLICK SLIDER)
  8. CLIENT BRAND SLIDER (SLICK SLIDER)
  9. PRICE SLIDER  (noUiSlider SLIDER)
  10. SCROLL TOP BUTTON
  11. PRELOADER
  12. GRID AND LIST LAYOUT CHANGER 
  13. RELATED ITEM SLIDER (SLICK SLIDER)

  
**/

jQuery(function ($) {
    /* ----------------------------------------------------------- */
    /*  1. CARTBOX 
  /* ----------------------------------------------------------- */

    jQuery(".aa-cartbox").hover(
        function () {
            jQuery(this).find(".aa-cartbox-summary").fadeIn(500);
        },
        function () {
            jQuery(this).find(".aa-cartbox-summary").fadeOut(500);
        }
    );

    /* ----------------------------------------------------------- */
    /*  2. TOOLTIP
  /* ----------------------------------------------------------- */
    jQuery('[data-toggle="tooltip"]').tooltip();
    jQuery('[data-toggle2="tooltip"]').tooltip();

    /* ----------------------------------------------------------- */
    /*  3. PRODUCT VIEW SLIDER 
  /* ----------------------------------------------------------- */

    jQuery("#demo-1 .simpleLens-thumbnails-container img").simpleGallery({
        loading_image: "demo/images/loading.gif",
    });

    jQuery("#demo-1 .simpleLens-big-image").simpleLens({
        loading_image: "demo/images/loading.gif",
    });

    /* ----------------------------------------------------------- */
    /*  4. POPULAR PRODUCT SLIDER (SLICK SLIDER)
  /* ----------------------------------------------------------- */

    jQuery(".aa-popular-slider").slick({
        dots: false,
        infinite: false,
        speed: 300,
        slidesToShow: 4,
        slidesToScroll: 4,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    infinite: true,
                    dots: true,
                },
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2,
                },
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                },
            },
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ],
    });

    /* ----------------------------------------------------------- */
    /*  5. FEATURED PRODUCT SLIDER (SLICK SLIDER)
  /* ----------------------------------------------------------- */

    jQuery(".aa-featured-slider").slick({
        dots: false,
        infinite: false,
        speed: 300,
        slidesToShow: 4,
        slidesToScroll: 4,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    infinite: true,
                    dots: true,
                },
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2,
                },
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                },
            },
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ],
    });

    /* ----------------------------------------------------------- */
    /*  6. LATEST PRODUCT SLIDER (SLICK SLIDER)
  /* ----------------------------------------------------------- */
    jQuery(".aa-latest-slider").slick({
        dots: false,
        infinite: false,
        speed: 300,
        slidesToShow: 4,
        slidesToScroll: 4,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    infinite: true,
                    dots: true,
                },
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2,
                },
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                },
            },
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ],
    });

    /* ----------------------------------------------------------- */
    /*  7. TESTIMONIAL SLIDER (SLICK SLIDER)
  /* ----------------------------------------------------------- */

    jQuery(".aa-testimonial-slider").slick({
        dots: true,
        infinite: true,
        arrows: false,
        speed: 300,
        slidesToShow: 1,
        adaptiveHeight: true,
    });

    /* ----------------------------------------------------------- */
    /*  8. CLIENT BRAND SLIDER (SLICK SLIDER)
  /* ----------------------------------------------------------- */

    jQuery(".aa-client-brand-slider").slick({
        dots: false,
        infinite: false,
        speed: 300,
        autoplay: true,
        autoplaySpeed: 2000,
        slidesToShow: 5,
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 4,
                    slidesToScroll: 4,
                    infinite: true,
                    dots: true,
                },
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2,
                },
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                },
            },
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ],
    });

    /* ----------------------------------------------------------- */
    /*  9. PRICE SLIDER  (noUiSlider SLIDER)
  /* ----------------------------------------------------------- */

    jQuery(function () {
        if ($("body").is(".productPage")) {
            var skipSlider = document.getElementById("skipstep");

            var filter_price_start = jQuery("#filter_price_start").val();
            var filter_price_end = jQuery("#filter_price_end").val();

            if (filter_price_start == "" || filter_price_end == "") {
                var filter_price_start = 100;
                var filter_price_end = 1700;
            }
            noUiSlider.create(skipSlider, {
                range: {
                    min: 0,
                    "10%": 100,
                    "20%": 200,
                    "30%": 300,
                    "40%": 400,
                    "50%": 500,
                    "60%": 600,
                    "70%": 700,
                    "80%": 800,
                    "90%": 900,
                    max: 1000,
                },
                snap: true,
                connect: true,
                // start: [100, 900],
                start: [filter_price_start, filter_price_end],
            });
            // for value print
            var skipValues = [
                document.getElementById("skip-value-lower"),
                document.getElementById("skip-value-upper"),
            ];

            skipSlider.noUiSlider.on("update", function (values, handle) {
                skipValues[handle].innerHTML = values[handle];
            });
        }
    });

    /* ----------------------------------------------------------- */
    /*  10. SCROLL TOP BUTTON
  /* ----------------------------------------------------------- */

    //Check to see if the window is top if not then display button

    jQuery(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $(".scrollToTop").fadeIn();
        } else {
            $(".scrollToTop").fadeOut();
        }
    });

    //Click event to scroll to top

    jQuery(".scrollToTop").click(function () {
        $("html, body").animate({ scrollTop: 0 }, 800);
        return false;
    });

    /* ----------------------------------------------------------- */
    /*  11. PRELOADER
  /* ----------------------------------------------------------- */

    jQuery(window).load(function () {
        // makes sure the whole site is loaded
        jQuery("#wpf-loader-two").delay(200).fadeOut("slow"); // will fade out
    });

    /* ----------------------------------------------------------- */
    /*  12. GRID AND LIST LAYOUT CHANGER 
  /* ----------------------------------------------------------- */

    jQuery("#list-catg").click(function (e) {
        e.preventDefault(e);
        jQuery(".aa-product-catg").addClass("list");
    });
    jQuery("#grid-catg").click(function (e) {
        e.preventDefault(e);
        jQuery(".aa-product-catg").removeClass("list");
    });

    /* ----------------------------------------------------------- */
    /*  13. RELATED ITEM SLIDER (SLICK SLIDER)
  /* ----------------------------------------------------------- */

    jQuery(".aa-related-item-slider").slick({
        dots: false,
        infinite: false,
        speed: 300,
        slidesToShow: 4,
        slidesToScroll: 4,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    infinite: true,
                    dots: true,
                },
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2,
                },
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                },
            },
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ],
    });
});

// add to cart in product page
//when user click on wattage than sent value image dynamic and will set wattage;
function change_product_wattage_image(img, wattage) {
    $(".simpleLens-big-image-container").html(
        `<a data-lens-image=${img} class="simpleLens-lens-image"><img src=${img} class="simpleLens-big-image"></a>`
    );
    $("#wattage").val(wattage);
    // alert(wattage)
}

// store data in data base

function add_to_cart(product_id, wattage_str) {
    // alert(product_id);
    var wattage = $("#wattage").val();

    if (wattage_str == 0) {
        wattage = "no";
    }


    $("#product_id").val(product_id);
    $("#pqty").val(jQuery("#qty").val());

    if (wattage == "") {
        jQuery("#add_to_cart_msg").html(
            '<div class="alert alert-danger fade in alert-dismissible mt10"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>Please select wattage</div>'
        );
    } else {
        jQuery.ajax({
            url: "/add-to-cart",
            data: jQuery("#frmAddToCart").serialize(),
            type: "post",
            success: function (result) {

                console.log(result);

                // return;
                var totalPrice = 0;
                alert("Product " + result.msg);
                if (result.totalItem == 0) {
                    jQuery(".aa-cart-notify").html("0");
                    jQuery(".aa-cartbox-summary").remove();
                } else {
                    jQuery(".aa-cart-notify").html(result.totalItem);
                    var html = "<ul>";
                    jQuery.each(result.data, function (arrKey, arrVal) {
                        totalPrice =
                            parseInt(totalPrice) +
                            parseInt(arrVal.qty) * parseInt(arrVal.price);
                        html +=
                            '<li><a class="aa-cartbox-img" href="#"><img src="' +
                            PRODUCT_IMAGE +
                            "/" +
                            arrVal.image +
                            '" alt="img"></a><div class="aa-cartbox-info"><h4><a href="#">' +
                            arrVal.title +
                            "</a></h4><p> " +
                            arrVal.qty +
                            " * Rs  " +
                            arrVal.price +
                            "</p></div></li>";
                    });
                }
                html +=
                    '<li><span class="aa-cartbox-total-title">Total</span><span class="aa-cartbox-total-price">Rs ' +
                    totalPrice +
                    "</span></li>";
                html +=
                    '</ul><a class="aa-cartbox-cart aa-primary-btn" href="/cart">View Cart</a>';
                console.log(html);
                jQuery(".aa-cartbox-summary").html(html);
            },
        });
    }
    // alert(product_attr_id);
}

function updateQty(pid, wattage, attr_id, price) {
    jQuery("#wattage").val(wattage);
    var qty = jQuery("#qty" + attr_id).val();
    jQuery("#qty").val(qty);
    add_to_cart(pid, wattage);
    jQuery("#total_price_" + attr_id).html("Rs " + qty * price);
}

function deleteCartProduct(pid, wattage, attr_id) {
    // alert(pid);
    jQuery("#wattage").val(wattage);
    jQuery("#qty").val(0);
    add_to_cart(pid, wattage);
    //jQuery('#total_price_'+attr_id).html('Rs '+qty*price);
    jQuery("#cart_box" + attr_id).hide();
}

function home_add_to_cart(id, wattage) {
    jQuery("#wattage").val(wattage);
    add_to_cart(id, wattage);
}

function sort_by() {
    var sort_by_value = jQuery("#sort_by_value").val();
    jQuery("#sort").val(sort_by_value);

    // send data query param in server
    jQuery("#categoryFilter").submit();
}

function sort_price_filter() {
    jQuery("#filter_price_start").val(jQuery("#skip-value-lower").html());
    jQuery("#filter_price_end").val(jQuery("#skip-value-upper").html());
    jQuery("#categoryFilter").submit();
}

function showWattage(wattage) {
    // $(".product_cct").hide();
    // $(".wattage_"+wattage).show();

    $("#wattage").val(wattage);
}

jQuery("#frmPlaceOrder").submit(function (e) {
    e.preventDefault();

    jQuery("#order_place_msg").html("Please wait...");

    jQuery.ajax({
        url: "/place_order",
        type: "POST",
        data: jQuery("#frmPlaceOrder").serialize(),
        success: function (result) {

            if (result.status === "success") {
                    window.location.href = result.redirect;

                // window.location.href = "/order-detail/" + result.order_id;
                return;
            }

            jQuery("#order_place_msg").html(result.msg);
        },
        error: function () {
            jQuery("#order_place_msg").html("Something went wrong. Please try again.");
        }
    });
});




function changeQty(change) {
    let qtyInput = document.getElementById('qty');
    let currentQty = parseInt(qtyInput.value) || 1;
    let newQty = currentQty + change;
    // limits
    if (newQty < 1) newQty = 1;
    if (newQty > 10) newQty = 10;
    qtyInput.value = newQty;
    qtyChanged(); // trigger event
}

function qtyChanged() {
    let qty = document.getElementById('qty').value;
    $('#pqty').val(qty);
}





let selected = {
    wattage: null,
    cct: null,
    color: null
};

$(document).ready(function () {
    // ================= CLICK EVENTS =================
    $('.wattage_link').click(function () {
        if ($(this).hasClass('disabled')) return;
        selected.wattage = $(this).data('wattage');

        $('#wattage').val(selected.wattage);
        activate('.wattage_link', $(this));
        filterVariants();
    });

    $('.cct_link').click(function () {
        if ($(this).hasClass('disabled')) return;
        selected.cct = $(this).data('cct');
        $('#cct').val(selected.cct);
        activate('.cct_link', $(this));
        filterVariants();
    });

    $('.color-circle').click(function () {
        if ($(this).hasClass('disabled')) return;
        selected.color = $(this).data('color');
        $('#color').val(selected.color);
        activate('.color-circle', $(this));
        filterVariants();
    });

    // ================= FUNCTIONS =================
    function activate(selector, $el) {
        $(selector).removeClass('active');
        $el.addClass('active');
    }

    function filterVariants() {
        let valid = window.VARIANTS.filter(function (v) {
            return (!selected.wattage || v.wattage == selected.wattage) &&
                (!selected.cct || v.cct == selected.cct) &&
                (!selected.color || v.color == selected.color);
        });

        disableOptions(valid);
        updatePrice(valid);
    }

    function disableOptions(valid) {
        // $('.wattage_link').each(function() {
        //     toggle($(this), valid.some(v => v.wattage == $(this).data('wattage')));
        // });

        $('.cct_link').each(function () {
            toggle($(this), valid.some(v => v.cct == $(this).data('cct')));
        });

        $('.color-circle').each(function () {
            toggle($(this), valid.some(v => v.color == $(this).data('color')));
        });
    }

    function toggle($el, enable) {
        $el.toggleClass('disabled', !enable);
    }

    function updatePrice(valid) {
        let finalVariant = valid;
        if (selected.color) {
            finalVariant = valid.filter(v => v.color == selected.color);
        }

        if (finalVariant.length === 1) {
            let price = finalVariant[0].price;
            let mrp = finalVariant[0].mrp;

            // Update main price
            $('.aa-price-block > .aa-product-view-price').first().text('Rs ' + price);

            // Update MRP if exists
            if (mrp && mrp > price) {
                $('.aa-price-block > .aa-product-view-price').eq(1).html('<del>Rs ' + mrp + '</del>');
            } else {
                $('.aa-price-block > .aa-product-view-price').eq(1).html('');
            }

            // Update stock
            $('.aa-product-avilability span').text(finalVariant[0].stock);
        } else if (window.VARIANTS.length > 0) {
            // Reset to first variant
            let price = window.VARIANTS[0].price;
            let mrp = window.VARIANTS[0].mrp;

            $('.aa-price-block > .aa-product-view-price').first().text('Rs ' + price);

            if (mrp && mrp > price) {
                $('.aa-price-block > .aa-product-view-price').eq(1).html('<del>Rs ' + mrp + '</del>');
            } else {
                $('.aa-price-block > .aa-product-view-price').eq(1).html('');
            }

            $('.aa-product-avilability span').text(window.VARIANTS[0].stock);
        }
    }


});
