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
                @canany(["update", "view", "delete"], \App\AddonType::class)
                    <th>Действия</th>
                @endcanany
            </tr>
            </thead>
            <tbody>
            @foreach ($types as $item)
                <tr>
                    <td>{{ $item->title }}</td>
                    <td>{{ $item->slug }}</td>
                    @canany(["update", "view", "delete"], $item)
                        <td>
                            <div role="toolbar" class="btn-toolbar">
                                <div class="btn-group mr-1">
                                    @can("update", $item)
                                        <a href="{{ route("admin.addon-types.edit", ["type" => $item]) }}" class="btn btn-primary">
                                            <i class="far fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can("view", $item)
                                        <a href="{{ route('admin.addon-types.show', ['type' => $item]) }}" class="btn btn-dark">
                                            <i class="far fa-eye"></i>
                                        </a>
                                    @endcan
                                    @can("delete", $item)
                                        <button type="button" class="btn btn-danger" data-confirm="{{ "delete-form-{$item->id}" }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @endcan
                                </div>
                            </div>
                            @can("delete", $item)
                                <confirm-form :id="'{{ "delete-form-{$item->id}" }}'">
                                    <template>
                                        <form action="{{ route('admin.addon-types.destroy', ['type' => $item]) }}"
                                              id="delete-form-{{ $item->id }}"
                                              class="btn-group"
                                              method="post">
                                            @csrf
                                            <input type="hidden" name="_method" value="DELETE">
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

@if ($types->lastPage() > 1)
    <div class="card-footer">
        {{ $types->links() }}
    </div>
@endif
