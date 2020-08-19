<div class="col-12">
    <div class="category-children">
        @foreach ($children as $child)
            <a href="{{ route("catalog.categories.show", ["category" => $child]) }}"
               class="btn btn-sm btn-outline-primary category-children__item">
                {{ $child->title }}
            </a>
        @endforeach
    </div>
</div>