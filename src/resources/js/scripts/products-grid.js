(function ($) {
    $(document).ready(function(){
        initCatalogSwitcher();
    });

    function initCatalogSwitcher() {
        let $switcher = $("#catalog-switcher");
        let $grid = $("#product-grid");
        if (! $switcher.length || ! $grid.length) {
            return;
        }
        let $switcherList = $switcher.find(".list");
        let $switcherBar = $switcher.find(".bar");
        if (! $switcherBar.length || ! $switcherList.length) {
            return;
        }
        $switcherList.click(function (event) {
            event.preventDefault();
            if ($switcherBar.hasClass("active")) {
                $switcherBar.removeClass("active");
                $(this).addClass("active");
            }
            if (! $grid.hasClass("products-grid_list")) {
                $grid.addClass("products-grid_list");
                setCookie("list");
            }
        });
        $switcherBar.click(function (event) {
            event.preventDefault();
            if ($switcherList.hasClass("active")) {
                $switcherList.removeClass("active");
                $(this).addClass("active");
            }
            if ($grid.hasClass("products-grid_list")) {
                $grid.removeClass("products-grid_list");
                setCookie("bar");
            }
        });
    }

    function setCookie(value) {
        axios
            .put(cookieUrl, {
                view: value
            })
    }

})(jQuery);
