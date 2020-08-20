<div class="card category-teaser">
    <a href="{{ route("catalog.categories.show", ["category" => $category]) }}"
       class="category-teaser__image">
        @if ($category->image)
            @pic([
            "image" => $category->image,
            "template" => "category-teaser-xs",
            "grid" => [
            "category-teaser-xl" => 1200,
            "category-teaser-lg" => 992,
            "category-teaser-md" => 768,
            "category-teaser-sm" => 576,
            ],
            "imgClass" => "card-img-top",
            ])
        @else
            <div class="category-teaser__empty">
                <svg class="category-teaser__empty-ico">
                    <use xlink:href="#catalog-empty-image"></use>
                </svg>
            </div>
        @endif
    </a>
    <div class="card-body category-teaser__body">
        <a class="category-teaser__title"
           href="{{ route("catalog.categories.show", ["category" => $category]) }}">
            {{ $category->title }}
        </a>
        @if (! empty($category->short))
            <div data-toggle="tooltip" data-placement="left" title="{{ $category->short }}">
                <svg class="category-teaser__question">
                    <use xlink:href="#catalog-question"></use>
                </svg>
            </div>
        @endif
    </div>
</div>