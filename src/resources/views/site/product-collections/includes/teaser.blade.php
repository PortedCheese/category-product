<div class="card category-teaser">
    <a href="{{ route("catalog.product-collections.show", ["collection" => $collection]) }}"
       class="catalog-image{{ config("category-product.useSimpleTeaser") ? "" : " catalog-image_upsize" }}">
        @if ($collection->image)
            @pic([
                "image" => $collection->image,
                "template" => "catalog-teaser-xs",
                "grid" => [
                    "catalog-teaser-xxl" => 1400,
                    "catalog-teaser-xl" => 1200,
                    "catalog-teaser-lg" => 992,
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
    <div class="card-body category-teaser__body">
        <a class="category-teaser__title"
           href="{{ route("catalog.product-collections.show", ["collection" => $collection]) }}">
            {{ $collection->title }}
        </a>
        @if (! empty($category->short))
            <p class="category-teaser__short">
                {{ $collection->short }}
            </p>
        @endif
    </div>
</div>