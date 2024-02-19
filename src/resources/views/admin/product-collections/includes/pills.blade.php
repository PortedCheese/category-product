<div class="col-12 mb-2">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-pills">
                @can("viewAll", \App\ProductCollection::class)
                    <li class="nav-item">
                        <a href="{{ route("admin.product-collections.index") }}"
                           class="nav-link{{ $currentRoute === "admin.product-collections.index" &&  !$isTree ? " active" : "" }}">
                            Список
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.product-collections.index') }}?view=tree"
                           class="nav-link{{ isset($isTree) && $isTree ? " active" : "" }}">
                            Приоритет
                        </a>
                    </li>
                @endcan

                @can("create", \App\ProductCollection::class)
                    <li class="nav-item">
                        <a href="{{ route("admin.product-collections.create") }}"
                           class="nav-link{{ $currentRoute === "admin.product-collections.create" ? " active" : "" }}">
                            Добавить
                        </a>
                    </li>
                @endcan

                @if (! empty($collection))
                    @can("view", \App\ProductCollection::class)
                        <li class="nav-item">
                            <a href="{{ route("admin.product-collections.show", ["collection" => $collection]) }}"
                               class="nav-link{{ $currentRoute === "admin.product-collections.show" ? " active" : "" }}">
                                Просмотр
                            </a>
                        </li>
                    @endcan
                    @can("update", \App\ProductCollection::class)
                        <li class="nav-item">
                            <a href="{{ route("admin.product-collections.edit", ["collection" => $collection]) }}"
                               class="nav-link{{ $currentRoute === "admin.product-collections.edit" ? " active" : "" }}">
                                Редактировать
                            </a>
                        </li>
                    @endcan
                    @can("viewAll", \App\Meta::class)
                            <li class="nav-item">
                                <a href="{{ route("admin.product-collections.metas", ["collection" => $collection]) }}"
                                   class="nav-link{{ $currentRoute === "admin.product-collections.metas" ? " active" : "" }}">
                                    Метатеги
                                </a>
                            </li>
                    @endcan

                    @can("delete", \App\ProductCollection::class)
                        <li class="nav-item">
                            <button type="button" class="btn btn-link nav-link"
                                    data-confirm="{{ "delete-form-collection-{$collection->id}" }}">
                                <i class="fas fa-trash-alt text-danger"></i>
                            </button>
                            <confirm-form :id="'{{ "delete-form-collection-{$collection->id}" }}'">
                                <template>
                                    <form action="{{ route('admin.product-collections.destroy', ['collection' => $collection]) }}"
                                          id="delete-form-collection-{{ $collection->id }}"
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