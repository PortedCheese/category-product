<div class="row justify-content-between">
    <div class="col">
        @include("category-product::site.products.includes.grid-sort")
    </div>

    @if (! empty($filters))
        <div class="col d-lg-none">
            @include("category-product::site.products.includes.grid-filters-modal")
        </div>
    @endif

    <div class="col d-none d-md-block">
        @include("category-product::site.products.includes.grid-view")
    </div>
</div>