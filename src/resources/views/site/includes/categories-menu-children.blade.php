<li>
    <a href="{{ $item["siteUrl"] }}" class="catalog-menu__link{{ $first ? " catalog-menu__link_bold" : "" }}">
        {{ $item["title"] }}
    </a>
    @if (! empty($item["children"]))
        <ul class="catalog-menu__list catalog-menu__list_level-{{ $level }}">
            @foreach ($item["children"] as $child)
                @include("category-product::site.includes.categories-menu-children", ["item" => $child, "first" => false, "level" => $level + 1])
            @endforeach
        </ul>
    @endif
</li>