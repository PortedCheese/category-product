<div class="col-12 col-sm-6 col-md-4 col-xl-3 category-teaser-cover">
    <div class="card category-teaser">
        @if ($category->image)
            <a href="{{ route("catalog.categories.show", ["category" => $category]) }}">
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
            </a>
        @endif
        <div class="card-body category-teaser__body">
            <a class="category-teaser__title"
               href="{{ route("catalog.categories.show", ["category" => $category]) }}">
                {{ $category->title }}
            </a>
            @if (! empty($category->short))
                <i class="far fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ $category->short }}"></i>
            @endif
        </div>
    </div>
</div>