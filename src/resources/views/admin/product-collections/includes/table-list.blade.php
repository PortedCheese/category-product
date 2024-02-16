<div class="card-header">
    <form action="{{ route($currentRoute) }}" method="get" class="form-inline">
        <label for="title" class="sr-only">Заголовок</label>
        <input type="text"
               id="title"
               placeholder="Заголовок"
               name="title"
               value="{{ $request->get("title", "") }}"
               class="form-control mb-2 mr-sm-2">

        <button type="submit" class="btn btn-primary mb-2 mr-sm-2">Применить</button>
        <a href="{{ route($currentRoute) }}" class="btn btn-secondary mb-2">Сбросить</a>
    </form>
</div>
<div class="card-body">
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>Заголовок</th>
                <th>Адрес</th>
                @canany(["update", "view", "delete"], \App\ProductCollection::class)
                    <th>Действия</th>
                @endcanany
            </tr>
            </thead>
            <tbody>
            @foreach ($collections as $item)
                <tr>
                    <td>{{ $item->title }}</td>
                    <td>{{ $item->slug }}</td>
                    @canany(["update", "view", "delete"], $item)
                        <td>
                            <div role="toolbar" class="btn-toolbar">
                                <div class="btn-group mr-1">
                                    @can("update", $item)
                                        <a href="{{ route("admin.product-collections.edit", ["collection" => $item]) }}" class="btn btn-primary">
                                            <i class="far fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can("view", $item)
                                        <a href="{{ route('admin.product-collections.show', ['collection' => $item]) }}" class="btn btn-dark">
                                            <i class="far fa-eye"></i>
                                        </a>
                                    @endcan
                                    @can("delete", $item)
                                        <button type="button" class="btn btn-danger" data-confirm="{{ "delete-form-{$item->id}" }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @endcan
                                    @can("publish", $item)
                                        <div class="btn-group">
                                            <button type="button"
                                                    class="btn btn-{{ $item->published_at ? "success" : "secondary" }}"
                                                    data-confirm="{{ "change-published-form-{$item->id}" }}">
                                                <i class="fas fa-toggle-{{ $item->published_at ? "on" : "off" }}"></i>
                                            </button>
                                        </div>
                                    @endcan
                                </div>
                            </div>
                            @can("delete", $item)
                                <confirm-form :id="'{{ "delete-form-{$item->id}" }}'">
                                    <template>
                                        <form action="{{ route('admin.product-collections.destroy', ['collection' => $item]) }}"
                                              id="delete-form-{{ $item->id }}"
                                              class="btn-group"
                                              method="post">
                                            @csrf
                                            <input type="hidden" name="_method" value="DELETE">
                                        </form>
                                    </template>
                                </confirm-form>
                            @endcan
                            @can("publish", $item)
                                <confirm-form :id="'{{ "change-published-form-{$item->id}" }}'"
                                              confirm-text="Да, изменить!"
                                              text="Это изменит статус показа коллекции на сайте">
                                    <template>
                                        <form id="change-published-form-{{ $item->id }}"
                                              action="{{ route("admin.product-collections.published", ['collection' => $item]) }}"
                                              method="post">
                                            @method('put')
                                            @csrf
                                        </form>
                                    </template>
                                </confirm-form>
                            @endcan
                        </td>
                    @endcanany
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@if ($collections->lastPage() > 1)
    <div class="card-footer">
        {{ $collections->links() }}
    </div>
@endif
