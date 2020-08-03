@if ($theme == "sb-admin")
    @php($active = (strstr($currentRoute, "admin.categories.") !== false) || (strstr($currentRoute, "admin.product.")) || (strstr($currentRoute, "admin.product-state.")))
    <li class="nav-item {{ $active ? " active" : "" }}">
        <a href="#"
           class="nav-link"
           data-toggle="collapse"
           data-target="#collapse-categories-menu"
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
                       class="collapse-item{{ $currentRoute == "admin.categories.index" ? " active" : "" }}">
                        <span>Список</span>
                    </a>
                @endcan
            </div>
        </div>
    </li>
@else
    <li class="nav-item dropdown">
        <a href="#"
           class="nav-link dropdown-toggle{{ (strstr($currentRoute, "admin.categories.") !== false) || (strstr($currentRoute, "admin.product.")) || (strstr($currentRoute, "admin.product-state.")) ? " active" : "" }}"
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
        </div>
    </li>
@endif