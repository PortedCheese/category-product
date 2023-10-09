<div class="table-responsive">
    <table class="table">
        <thead>
        <tr>
            <th>Заголовок</th>
            <th>Адресная строка</th>
            <th>Дочернии</th>
            @canany(["edit", "view", "delete"], \App\Category::class)
                <th>Действия</th>
            @endcanany
        </tr>
        </thead>
        <tbody>
        @foreach ($categories as $item)
            <tr>
                <td>{{ $item->title }}</td>
                <td>{{ $item->slug }}</td>
                <td>{{ $item->children->count() }}</td>
                @canany(["edit", "view", "delete"], \App\Category::class)
                    <td>
                        <div role="toolbar" class="btn-toolbar">
                            <div class="btn-group mr-1">
                                @can("update", \App\Category::class)
                                    <a href="{{ route("admin.categories.edit", ["category" => $item]) }}" class="btn btn-primary">
                                        <i class="far fa-edit"></i>
                                    </a>
                                @endcan
                                @can("view", \App\Category::class)
                                    <a href="{{ route('admin.categories.show', ['category' => $item]) }}" class="btn btn-dark">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can("delete", \App\Category::class)
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
                        @can("delete", \App\Category::class)
                            <confirm-form :id="'{{ "delete-form-{$item->id}" }}'">
                                <template>
                                    <form action="{{ route('admin.categories.destroy', ['category' => $item]) }}"
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
                                          text="Это изменит статус показа категории и товаров на сайте">
                                <template>
                                    <form id="change-published-form-{{ $item->id }}"
                                          action="{{ route("admin.categories.published", ['category' => $item]) }}"
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