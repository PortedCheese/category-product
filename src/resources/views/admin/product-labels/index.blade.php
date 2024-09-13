@extends("admin.layout")

@section("page-title", "Метки товаров - ")

@section('header-title', "Метки товаров")

@section('admin')
    @include("category-product::admin.product-labels.includes.pills")
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <form action="{{ route($currentRoute) }}" method="get" class="d-lg-flex">
                    <label for="title" class="sr-only">Заголовок</label>
                    <input type="text"
                           id="title"
                           placeholder="Заголовок"
                           name="title"
                           value="{{ $request->get("title", "") }}"
                           class="form-control mb-2 me-sm-2">

                    <button type="submit" class="btn btn-primary mb-2 me-sm-2">Применить</button>
                    <a href="{{ route($currentRoute) }}" class="btn btn-secondary mb-2">Сбросить</a>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Заголовок</th>
                                <th>Цвет</th>
                                <th>Адрес</th>
                                @canany(["update", "view", "delete"], \App\ProductLabel::class)
                                    <th>Действия</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($labels as $item)
                            <tr>
                                <td>{{ $item->title }}</td>
                                <td>
                                    <span class="product-label product-label_{{ $item->color }}">
                                        {{ $item->color_name }}
                                    </span>
                                </td>
                                <td>{{ $item->slug }}</td>
                                @canany(["update", "view", "delete"], $item)
                                    <td>
                                        <div role="toolbar" class="btn-toolbar">
                                            <div class="btn-group me-1">
                                                @can("update", $item)
                                                    <a href="{{ route("admin.product-labels.edit", ["label" => $item]) }}" class="btn btn-primary">
                                                        <i class="far fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can("view", $item)
                                                    <a href="{{ route('admin.product-labels.show', ['label' => $item]) }}" class="btn btn-dark">
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
                                                    <form action="{{ route('admin.product-labels.destroy', ['label' => $item]) }}"
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
    @if ($labels->lastPage() > 1)
        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-body">
                    {{ $labels->links() }}
                </div>
            </div>
        </div>
    @endif
@endsection