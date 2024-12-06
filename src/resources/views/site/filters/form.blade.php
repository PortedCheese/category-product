<form action="{{ route("catalog.categories.show", ["category" => $category]) }}"
      method="get">
    <input type="hidden" name="sort-by" value="{{ $sortBy }}">
    <input type="hidden" name="sort-order" value="{{ $sortDirection }}">

    @foreach($filters as $filter)
        @switch($filter->type)
            @case("select")
                @include("category-product::site.filters.includes.select")
                @break

            @case("checkbox")
                @include("category-product::site.filters.includes.checkbox")
                @break

            @case("range")
                @include("category-product::site.filters.includes.range")
                @break
            @case("color")
                @include("category-product::site.filters.includes.color")
                @break
        @endswitch
    @endforeach

    <div class="form-group">
        <button type="submit" class="btn btn-primary w-100">Показать</button>
        <a href="{{ route("catalog.categories.show", ['category' => $category]) }}"
           class="btn btn-link w-100">
            Сбросить фильтры
        </a>
    </div>
</form>