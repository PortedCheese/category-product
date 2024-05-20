import jQueryBridget from "jquery-bridget";
import ProductFlickity from "flickity-as-nav-for";
import "flickity/css/flickity.css";
// Flickty as Jquery plugin
ProductFlickity.setJQuery($);
jQueryBridget( 'productFlickity', ProductFlickity, $ );

(function ($) {
    $(document).ready(function(){
        setTimeout(initProductCarousel, 1000);
    });

    function initProductCarousel() {
        if (! $(".product-gallery-top").length) return;

        let $galleryTop =  $(".product-gallery-top").productFlickity({
            prevNextButtons: false,
            pageDots: false,
            wrapAround: true,
            cellSelector: '.carousel-cell',
            draggable: false
        });

        if (! $(".product-gallery-thumbs").length) return;
        let $galleryThumbs =  $(".product-gallery-thumbs").productFlickity({
                prevNextButtons: false,
                pageDots: false,
                asNavFor: ".product-gallery-top",
                freeScroll: true,
                contain: true,
        });

        if (! $(".product-gallery-btns").length) return;
          $('.product-gallery-btns').on( 'click', '.btn', function()  {
            var selector = $(this).attr('data-selector');
            $galleryTop.productFlickity( 'selectCell', selector );
        });

    }
})(jQuery);