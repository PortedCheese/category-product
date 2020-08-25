<div class="card product-teaser">
    <a href="{{ route("catalog.products.show", ["product" => $product]) }}"
       class="catalog-image catalog-image_product{{ config("category-product.useSimpleTeaser") ? "" : " catalog-image_upsize" }}">
        @if ($product->cover)
            @pic([
                "image" => $product->cover,
                "template" => "catalog-teaser-xs",
                "grid" => [
                    "catalog-teaser-xl" => 1200,
                    "product-teaser-lg" => 992,
                    "catalog-teaser-md" => 768,
                    "catalog-teaser-sm" => 576,
                ],
                "imgClass" => "card-img-top",
            ])
        @else
            <div class="catalog-image__empty">
                <svg class="catalog-image__empty-ico">
                    <use xlink:href="#catalog-empty-image"></use>
                </svg>
            </div>
        @endif
    </a>
    <div class="card-body">
        <a href="{{ route("catalog.products.show", ["product" => $product]) }}">
            {{ $product->title }}
        </a>
    </div>
</div>