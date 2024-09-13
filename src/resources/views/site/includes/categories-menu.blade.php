<li class="nav-item dropdown catalog-menu">
    <a href="#"
       id="catalog-menu"
       class="nav-link dropdown-toggle"
       data-bs-toggle="dropdown"
       aria-haspopup="true"
       aria-expanded="false">
        Каталог
    </a>
    <div class="dropdown-menu catalog-menu__dropdown"
        aria-labelledby="catalog-menu">
        <div class="catalog-menu__container">
            @foreach ($categoriesTree as $item)
                <ul class="catalog-menu__list">
                    @include("category-product::site.includes.categories-menu-children", ["item" => $item, "first" => true, "level" => 1])
                </ul>
            @endforeach
        </div>
    </div>
</li>