@php
    $active = (strstr($currentRoute, ".categories.") !== false) ||
              (strstr($currentRoute, ".products.") !== false) ||
              (strstr($currentRoute, ".specification-groups.") !== false) ||
              (strstr($currentRoute, ".specifications.") !== false) ||
              (strstr($currentRoute, ".product-labels.") !== false) ||
              (strstr($currentRoute, ".product-collections.") !== false) ||
              (strstr($currentRoute, ".measurements.") !== false) ||
              (strstr($currentRoute, ".addon-types.") !== false);
@endphp

@if ($theme == "sb-admin")
    <li class="nav-item {{ $active ? " active" : "" }}">
        <a href="#"
           class="nav-link"
           data-bs-toggle="collapse"
           data-bs-target="#collapse-categories-menu"
           aria-controls="#collapse-categories-menu"
           aria-expanded="{{ $active ? "true" : "false" }}">
            <i class="fas fa-stream"></i>
            <span>Категории</span>
        </a>

        <div id="collapse-categories-menu"
             class="collapse"
             data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @can("viewAny", \App\Category::class)
                    <a href="{{ route("admin.categories.index") }}"
                       class="collapse-item{{ strstr($currentRoute, ".categories.") !== false ? " active" : "" }}">
                        <span>Список</span>
                    </a>
                @endcan

                @can("viewAny", \App\SpecificationGroup::class)
                    <a href="{{ route("admin.specification-groups.index") }}"
                       class="collapse-item{{ strstr($currentRoute, ".specification-groups.") !== false ? " active" : "" }}">
                        <span>Группы характеристик</span>
                    </a>
                @endcan

                @can("viewAny", \App\Specification::class)
                    <a href="{{ route("admin.specifications.index") }}"
                       class="collapse-item{{ strstr($currentRoute, "admin.specifications.") !== false ? " active" : "" }}">
                        <span>Характеристики</span>
                    </a>
                @endcan

                @can("viewAny", \App\ProductLabel::class)
                    <a href="{{ route("admin.product-labels.index") }}"
                       class="collapse-item{{ strstr($currentRoute, ".product-labels.") !== false ? " active" : "" }}">
                        <span>Метки товаров</span>
                    </a>
                @endcan

                @can("viewAny", \App\ProductCollection::class)
                    <a href="{{ route("admin.product-collections.index") }}"
                       class="collapse-item{{ strstr($currentRoute, ".product-collections.") !== false ? " active" : "" }}">
                        <span>{{ config("category-product.productCollectionsName") }} товаров</span>
                    </a>
                @endcan

                @if (class_exists(\App\Measurement::class) && config("product-variation.enableVariations"))
                        @can("viewAny", \App\Measurement::class)
                            <a href="{{ route("admin.measurements.index") }}"
                               class="collapse-item{{ strstr($currentRoute, ".measurements.") !== false ? " active" : "" }}">
                                <span>Измерения</span>
                            </a>
                        @endcan
                @endif

                @can("viewAny", \App\Product::class)
                    <a href="{{ route("admin.products.index") }}"
                       class="collapse-item{{ strstr($currentRoute, "admin.products.index") !== false ? " active" : "" }}">
                        <span>Товары</span>
                    </a>
                @endcan

                    @if(config("category-product.useAddons",false))
                        @can("viewAny", \App\AddonType::class)
                            <a href="{{ route("admin.addon-types.index") }}"
                               class="collapse-item{{ strstr($currentRoute, "admin.addon-types.index") !== false ? " active" : "" }}">
                                <span>{{ config("category-product.addonTypesName") }}</span>
                            </a>
                        @endcan
                    @endif
            </div>
        </div>
    </li>
@else
    <li class="nav-item dropdown">
        <a href="#"
           class="nav-link dropdown-toggle{{ $active ? " active" : "" }}"
           role="button"
           id="categories-menu"
           data-toggle="dropdown"
           aria-haspopup="true"
           aria-expanded="false">
            <i class="fas fa-stream"></i>
            Категории
        </a>

        <div class="dropdown-menu" aria-labelledby="categories-menu">
            @can("viewAny", \App\Category::class)
                <a href="{{ route("admin.categories.index") }}"
                   class="dropdown-item">
                    Список
                </a>
            @endcan
            @can("viewAny", \App\SpecificationGroup::class)
                <a href="{{ route("admin.specification-groups.index") }}"
                   class="dropdown-item">
                    Группы характеристик
                </a>
            @endcan
            @can("viewAny", \App\Specification::class)
                <a href="{{ route("admin.specifications.index") }}"
                   class="dropdown-item">
                    Характеристики
                </a>
            @endcan
            @can("viewAny", \App\ProductLabel::class)
                <a href="{{ route("admin.product-labels.index") }}"
                   class="dropdown-item">
                    Метки товаров
                </a>
            @endcan
            @can("viewAny", \App\Product::class)
                <a href="{{ route("admin.products.index") }}"
                   class="dropdown-item">
                    Товары
                </a>
            @endcan
        </div>
    </li>
@endif

@includeIf("product-variation::admin.menu")