<div class="product-switch-view"
     id="catalog-switcher"
     role="group"
     aria-label="Изменить отображение товаров">
    <button type="button"
            class="btn list product-switch-view__btn{{ $productView == "list" ? " active" : "" }}">
        <svg class="product-switch-view__ico">
            <use xlink:href="#catalog-list-view"></use>
        </svg>
    </button>
    <button type="button"
            class="btn bar product-switch-view__btn{{ $productView == "bar" ? " active" : "" }}">
        <svg class="product-switch-view__ico">
            <use xlink:href="#catalog-bar-view"></use>
        </svg>
    </button>
</div>