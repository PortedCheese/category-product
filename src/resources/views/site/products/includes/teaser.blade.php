<div class="card product-teaser{{ ! $product->published_at ? ' product-teaser_disable' : '' }}">
    <div class="product-teaser__image-cover">
        @include("category-product::site.products.includes.favorite", ["teaser" => true, "product" => $product])
        @if ($product->labels->count())
            <div class="product-teaser__labels">
                @foreach ($product->labels as $label)
                    <a href="{{ route("catalog.labels.show", ["label" => $label]) }}"
                       class="product-teaser__label product-label product-label_{{ $label->color }}">
                        {{ $label->title }}
                    </a>
                @endforeach
            </div>
        @endif
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
    </div>
    <div class="card-body product-teaser__body">
        <div class="product-teaser__info">
            <a href="{{ route("catalog.products.show", ["product" => $product]) }}" class="product-teaser__title">
                {{ $product->title }}
            </a>
            @if ($product->short)
                <div class="product-teaser__short">{{ $product->short }}</div>
            @endif
        </div>
    </div>
    @isset($product->published_at)
        <div class="card-footer product-teaser__footer">
            @includeFirst(["product-variation::site.variations.teaser-price", "category-product::site.products.includes.teaser-footer"])
        </div>
    @endisset
</div>