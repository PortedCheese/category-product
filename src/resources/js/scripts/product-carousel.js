import ProductFlickity from "flickity-as-nav-for";
import "flickity/css/flickity.css";

(function ($) {
    $(document).ready(function(){
        initProductCarousel();
    });

    function initProductCarousel() {
        if (! $(".product-gallery-top").length) return;

        let top = document.querySelector(".product-gallery-top");
        let galleryTop = new ProductFlickity(top, {
            prevNextButtons: false,
            pageDots: false,
            wrapAround: true,
            cellSelector: '.carousel-cell'
        });

        if (! $(".product-gallery-thumbs").length) return;

        let thumb = document.querySelector(".product-gallery-thumbs");
        let galleryThumbs = new ProductFlickity(thumb, {
            prevNextButtons: false,
            pageDots: false,
            asNavFor: ".product-gallery-top",
            freeScroll: true,
            contain: true,
        })
    }
})(jQuery);