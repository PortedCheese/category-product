@extends("admin.layout")

@section("page-title", "Просмотр {$label->title} - ")

@section('header-title', "Просмотр {$label->title}")

@section('admin')
    @include("category-product::admin.product-labels.includes.pills")
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <form action="{{ route($currentRoute, ["label" => $label]) }}" method="get" class="form-inline">
                    <label for="title" class="sr-only">Заголовок</label>
                    <input type="text"
                           id="title"
                           placeholder="Заголовок"
                           name="title"
                           value="{{ $request->get("title", "") }}"
                           class="form-control mb-2 mr-sm-2">

                    <button type="submit" class="btn btn-primary mb-2 mr-sm-2">Применить</button>
                    <a href="{{ route($currentRoute, ["label" => $label]) }}" class="btn btn-secondary mb-2">Сбросить</a>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Заголовок</th>
                            <th>Категория</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product->title }}</td>
                                <td>
                                    <a href="{{ route('admin.categories.show', ['category' => $product->category]) }}">
                                        {{ $product->category->title }}
                                    </a>
                                </td>
                                <td>
                                    <div role="toolbar" class="btn-toolbar">
                                        <div class="btn-group mr-1">
                                            @can("update", $product)
                                                <a href="{{ route('admin.products.edit', ['product' => $product]) }}"
                                                   class="btn btn-primary">
                                                    <i class="far fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can("view", $product)
                                                <a href="{{ route('admin.products.show', ['product' => $product]) }}"
                                                   class="btn btn-dark">
                                                    <i class="far fa-eye"></i>
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if ($products->LastPage() > 1)
        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-body">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    @endif
@endsection