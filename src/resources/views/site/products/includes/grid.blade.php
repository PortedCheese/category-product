<div id="product-grid" class="row products-grid{{ $productView == "list" ? " products-grid_list" : "" }}">
    @foreach ($products as $item)
        <div class="col-12 col-sm-6 col-md-4 products-grid-col">
            @include("category-product::site.products.includes.teaser", ["product" => $item->getTeaserData()])
        </div>
    @endforeach
</div>