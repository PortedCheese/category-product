@include("category-product::admin.categories.includes.pills")
<div class="col-12 mb-2">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-pills">
                @can("viewAny", \App\Product::class)
                    <li class="nav-item">
                        <a href="{{ route("admin.categories.products.index", ["category" => $category]) }}"
                           class="nav-link{{ $currentRoute === "admin.categories.products.index" ? " active" : "" }}">
                            Список
                        </a>
                    </li>
                @endcan

                @can("create", \App\Product::class)
                    <li class="nav-item dropdown">
                        <a href="#"
                           class="nav-link dropdown-toggle{{ $currentRoute === "admin.categories.products.create" ? " active" : "" }}"
                           data-toggle="dropdown"
                        >
                            Добавить
                        </a>
                        <div class="dropdown-menu">
                            <a href="{{ route("admin.categories.products.create", ["category" => $category]) }}"
                               class="dropdown-item{{ $currentRoute === "admin.categories.products.create" && ! \Illuminate\Support\Facades\Request::get("addon") ? ' active':''}}">
                                Товар
                            </a>
                            @if(config("category-product.useAddons",false))
                                <a href="{{ route("admin.categories.products.create", ["category" => $category, "addon" => true]) }}"
                                   class="dropdown-item{{ $currentRoute === "admin.categories.products.create" && \Illuminate\Support\Facades\Request::get("addon") ? ' active':''}}">
                                    Товар-дополнение
                                </a>
                            @endif
                        </div>
                    </li>

                @endcan

                @if (! empty($product))
                    @can("view", $product)
                        <li class="nav-item">
                            <a href="{{ route("admin.products.show", ["product" => $product]) }}"
                               class="nav-link{{ $currentRoute === "admin.products.show" ? " active" : "" }}">
                                Просмотр
                            </a>
                        </li>
                    @endcan

                    @can("update", $product)
                        <li class="nav-item">
                            <a href="{{ route("admin.products.edit", ["product" => $product]) }}"
                               class="nav-link{{ $currentRoute === "admin.products.edit" ? " active" : "" }}">
                                Редактировать
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route("admin.products.gallery", ["product" => $product]) }}"
                               class="nav-link{{ $currentRoute === "admin.products.gallery" ? " active" : "" }}">
                                Галерея
                            </a>
                        </li>
                    @endcan

                    @can("specificationManagement", $product)
                        <li class="nav-item">
                            <a href="{{ route("admin.products.specifications.index", ["product" => $product]) }}"
                               class="nav-link{{ strstr($currentRoute, ".products.specifications.") !== false ? " active" : "" }}">
                                Характеристики
                            </a>
                        </li>
                    @endcan

                    @if (! $product->addonType)
                        @can("viewAny", \App\Meta::class)
                            @can("update", $product)
                                <li class="nav-item">
                                    <a href="{{ route("admin.products.meta", ["product" => $product]) }}"
                                       class="nav-link{{ $currentRoute === "admin.products.meta" ? " active" : "" }}">
                                        Метатеги
                                    </a>
                                </li>
                            @endcan
                        @endcan
                    @endif

                    @can("delete", $product)
                        <li class="nav-item">
                            <button type="button" class="btn btn-link nav-link"
                                    data-confirm="{{ "delete-form-product-{$product->id}" }}">
                                <i class="fas fa-trash-alt text-danger"></i>
                            </button>
                            <confirm-form :id="'{{ "delete-form-product-{$product->id}" }}'">
                                <template>
                                    <form action="{{ route('admin.products.destroy', ['product' => $product]) }}"
                                          id="delete-form-product-{{ $product->id }}"
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