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
                    <li class="nav-item">
                        <a href="{{ route("admin.categories.products.create", ["category" => $category]) }}"
                           class="nav-link{{ $currentRoute === "admin.categories.products.create" ? " active" : "" }}">
                            Добавить
                        </a>
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