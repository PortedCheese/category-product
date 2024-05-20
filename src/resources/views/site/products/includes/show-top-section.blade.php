<div class="col-12 col-lg-6">
    <div class="product-gallery-top">
        @foreach ($gallery as $item)
            <div class="carousel-cell cell{{ $item["id"] }}">
                @img([
                    "image" => $item,
                    "template" => "product-show-xl",
                    "lightbox" => "lightGroupProduct",
                    "grid" => [
                        "product-show-xl" => 1200,
                        "product-show-lg" => 992,
                        "product-show-md" => 768,
                        "product-show-sm" => 576,
                    ],
                    "imgClass" => "img-fluid rounded",
                ])
            </div>
        @endforeach
        @if ($product->labels->count())
            <div class="product-show__labels">
                @foreach ($product->labels as $label)
                    <a href="{{ route("catalog.labels.show", ["label" => $label]) }}"
                       class="product-show__label product-label product-label_{{ $label->color }}">
                        {{ $label->title }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    @if ($gallery->count() >= 2)
        <div class="product-gallery-thumbs">
            @foreach ($gallery as $item)
                <div class="carousel-cell">
                    @pic([
                    "image" => $item,
                    "template" => "product-show-thumb",
                    "grid" => [],
                    "imgClass" => "img-fluid rounded",
                    ])
                </div>
            @endforeach
        </div>
    @endif
</div>
<div class="col-12 col-lg-6">
    <div class="product-show__text-cover">
        <h1 class="product-show__title">{{ $product->title }}</h1>
        @include("category-product::site.products.includes.favorite")
        @if ($product->published_at)
            @includeFirst([
                    "variation-cart::site.variations.show",
                    "product-variation::site.variations.show",
                    "category-product::site.products.includes.short",
                ], ["product" => $product])
        @else
            @include("category-product::site.products.includes.out-of-stock")
        @endif
        @if (count($product->collectionsPublished()))
            <p class="text-muted">Подборки:</p>
            @foreach($product->collectionsPublished() as $item)
                @includeIf('category-product::site.product-collections.includes.teaser-short', ['collection' => $item])
            @endforeach
        @endif

    </div>
</div>