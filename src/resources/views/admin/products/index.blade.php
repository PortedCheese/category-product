@extends("admin.layout")

@section("page-title", "Товары - ")

@section('header-title')
    @empty($category)
        Товары
    @else
        {{ $category->title }}
    @endempty
@endsection

@section('admin')
    @isset($category)
        @include("category-product::admin.products.includes.pills")
    @endisset
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <form action="{{ $fromRoute }}" method="get" class="form-inline">

                   @include("category-product::admin.products.includes.product-filter")

                    <label for="title" class="sr-only">Заголовок</label>
                    <input type="text"
                           id="title"
                           name="title"
                           placeholder="Заголовок"
                           value="{{ $request->get("title", "") }}"
                           class="form-control  mb-2 mr-sm-2">

                    <select class="custom-select mb-2 mr-sm-2" name="published" aria-label="Статус публикации">
                        <option value="all"{{ ! $request->has('published') || $request->get('published') == 'all' ? " selected" : '' }}>
                            Статус публикации
                        </option>
                        <option value="yes"{{ $request->get('published') === 'yes' ? " selected" : '' }}>
                            Опубликованно
                        </option>
                        <option value="no"{{ $request->get('published') === 'no' ? " selected" : '' }}>
                            Снято с публикации
                        </option>
                    </select>

                    <button class="btn btn-primary mb-2 mr-2" type="submit">Применить</button>
                    <a href="{{ $fromRoute }}" class="btn btn-secondary mb-2">
                        Сбросить
                    </a>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Заголовок</th>
                            @empty($category)
                                <th>Категория</th>
                            @endempty
                            @canany(["update", "view", "delete", "publish"], \App\Product::class)
                                <th>Действия</th>
                            @endcanany
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($products as $item)
                            <tr>
                                <td class="{{ isset($item->addonType) ? "text-muted" : "" }}">{{ $item->title }}</td>
                                @empty($category)
                                    <td>
                                        <a href="{{ route("admin.categories.show", ["category" => $item->category]) }}" target="_blank">
                                            {{ $item->category->title }}
                                        </a>
                                    </td>
                                @endempty
                                @canany(["update", "view", "delete", "publish"], $item)
                                    <td>
                                        <div role="toolbar" class="btn-toolbar">
                                            <div class="btn-group mr-1">
                                                @can("update", $item)
                                                    <a href="{{ route("admin.products.edit", ["product" => $item]) }}" class="btn btn-primary">
                                                        <i class="far fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can("view", $item)
                                                    <a href="{{ route('admin.products.show', ['product' => $item]) }}" class="btn btn-dark">
                                                        <i class="far fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can("delete", $item)
                                                    <button type="button" class="btn btn-danger" data-confirm="{{ "delete-form-{$item->id}" }}">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                @endcan
                                            </div>
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
                                        @can("publish", $item)
                                            <confirm-form :id="'{{ "change-published-form-{$item->id}" }}'"
                                                          confirm-text="Да, изменить!"
                                                          text="Это изменит статус показа товара на сайте">
                                                <template>
                                                    <form id="change-published-form-{{ $item->id }}"
                                                          action="{{ route("admin.products.published", ['product' => $item]) }}"
                                                          method="post">
                                                        @method('put')
                                                        @csrf
                                                    </form>
                                                </template>
                                            </confirm-form>
                                        @endcan
                                        @can("delete", $item)
                                            <confirm-form :id="'{{ "delete-form-{$item->id}" }}'">
                                                <template>
                                                    <form action="{{ route('admin.products.destroy', ['product' => $item]) }}"
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
        </div>
    </div>
    @if ($products->lastPage() > 1)
        <div class="col-12 mt-3">
            <div class="card">
                <div class="card-body">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    @endif
@endsection