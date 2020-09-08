<div id="product-grid" class="row products-grid{{ $productView == "list" ? " products-grid_list" : "" }}">
    @if ($products->count())
        @foreach ($products as $item)
            <div class="col-12 col-sm-6 col-md-4{{ $presentSidebar ? "" : " col-lg-3" }} products-grid-col">
                @include("category-product::site.products.includes.teaser", ["product" => $item->getTeaserData()])
            </div>
        @endforeach
    @else
        <div class="col-12">
            <p>
                По Вашему запросу ничего не найдено
            </p>
        </div>
    @endif
</div>