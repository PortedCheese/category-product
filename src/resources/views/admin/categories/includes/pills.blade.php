@if (! empty($category))
    @include("category-product::admin.categories.includes.breadcrumb")
@endif
<div class="col-12 mb-2">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-pills">
                @can("viewAny", \App\Category::class)
                    <li class="nav-item">
                        <a href="{{ route("admin.categories.index") }}"
                           class="nav-link{{ isset($isTree) && !$isTree ? " active" : "" }}">
                            Категории
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.categories.index') }}?view=tree"
                           class="nav-link{{ isset($isTree) && $isTree ? " active" : "" }}">
                            Структура
                        </a>
                    </li>
                @endcan

                @empty($category)
                    @can("create", \App\Category::class)
                        <li class="nav-item">
                            <a href="{{ route("admin.categories.create") }}"
                               class="nav-link{{ $currentRoute === "admin.categories.create" ? " active" : "" }}">
                                Добавить
                            </a>
                        </li>
                    @endcan
                @else
                    @can("create", \App\Category::class)
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ $currentRoute == 'admin.categories.create-child' ? " active" : "" }}"
                               data-toggle="dropdown"
                               href="#"
                               role="button"
                               aria-haspopup="true"
                               aria-expanded="false">
                                Добавить
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item"
                                   href="{{ route('admin.categories.create') }}">
                                    Основную
                                </a>
                                @if ($category->nesting < config("category-product.categoryNest"))
                                    <a class="dropdown-item"
                                       href="{{ route('admin.categories.create-child', ['category' => $category]) }}">
                                        Подкатегорию
                                    </a>
                                @endif
                            </div>
                        </li>
                    @endcan

                    @can("view", $category)
                        <li class="nav-item">
                            <a href="{{ route("admin.categories.show", ["category" => $category]) }}"
                               class="nav-link{{ $currentRoute === "admin.categories.show" ? " active" : "" }}">
                                Просмотр
                            </a>
                        </li>
                    @endcan

                    @can("update", $category)
                        <li class="nav-item">
                            <a href="{{ route("admin.categories.edit", ["category" => $category]) }}"
                               class="nav-link{{ $currentRoute === "admin.categories.edit" ? " active" : "" }}">
                                Редактировать
                            </a>
                        </li>
                    @endcan

                    @can("viewAny", \App\Meta::class)
                        <li class="nav-item">
                            <a href="{{ route("admin.categories.metas", ["category" => $category]) }}"
                               class="nav-link{{ $currentRoute === "admin.categories.metas" ? " active" : "" }}">
                                Метатеги
                            </a>
                        </li>
                    @endcan

                    @can("viewAny", \App\Specification::class)
                        @php($active = strstr($currentRoute, "categories.specifications.") !== false || strstr($currentRoute, "admin.specifications.") !== false)
                        <li class="nav-item">
                            <a href="{{ route("admin.categories.specifications.index", ["category" => $category]) }}"
                               class="nav-link{{ $active ? " active" : "" }}">
                                Характеристики <span class="badge badge-dark">{{ $category->specifications->count() }}</span>
                            </a>
                        </li>
                    @endcan

                    @can("viewAny", \App\Product::class)
                        <li class="nav-item">
                            <a href="{{ route("admin.categories.products.index", ["category" => $category]) }}"
                               class="nav-link{{ strstr($currentRoute, "products.") !== false ? " active" : "" }}">
                                Товары
                            </a>
                        </li>
                    @endcan

                    @can("delete", $category)
                        <li class="nav-item">
                            <button type="button" class="btn btn-link nav-link"
                                    data-confirm="{{ "delete-form-category-{$category->id}" }}">
                                <i class="fas fa-trash-alt text-danger"></i>
                            </button>
                            <confirm-form :id="'{{ "delete-form-category-{$category->id}" }}'">
                                <template>
                                    <form action="{{ route('admin.categories.destroy', ['category' => $category]) }}"
                                          id="delete-form-category-{{ $category->id }}"
                                          class="btn-group"
                                          method="post">
                                        @csrf
                                        @method("delete")
                                    </form>
                                </template>
                            </confirm-form>
                        </li>
                    @endcan
                @endif
            </ul>
        </div>
    </div>
</div>