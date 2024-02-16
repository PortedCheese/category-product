@extends("admin.layout")

@section("page-title", "Просмотр {$collection->title} - ")

@section('header-title', "Просмотр {$collection->title}")

@section('admin')
    @include("category-product::admin.product-collections.includes.pills")
    @if ($collection->short || $collection->description || $collection->image)
        <div class="col-12 mb-2">
            <div class="card">
                <div class="card-body">
                    <dl class="row">
                        @if ($collection->image)
                            <dt class="col-sm-3">Изображение:</dt>
                            <dd class="col-sm-9">
                                @pic([
                                "image" =>  $collection->image,
                                "template" => "medium",
                                "grid" => [
                                ],
                                "imgClass" => "rounded mb-2",
                                ])
                            </dd>
                        @endif
                        @if ($collection->short)
                            <dt class="col-sm-3">Краткое описание:</dt>
                            <dd class="col-sm-9">
                                {{ $collection->short }}
                            </dd>
                        @endif
                        @if ($collection->description)
                            <dt class="col-sm-3">Описание:</dt>
                            <dd class="col-sm-9">
                                <div>{!! $collection->description !!}</div>
                            </dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    @endif
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <p><b>Товары коллекции:</b></p>
                <form action="{{ route($currentRoute, ["collection" => $collection]) }}" method="get" class="form-inline">
                    <label for="title" class="sr-only">Заголовок</label>
                    <input type="text"
                           id="title"
                           placeholder="Заголовок"
                           name="title"
                           value="{{ $request->get("title", "") }}"
                           class="form-control mb-2 mr-sm-2">

                    <button type="submit" class="btn btn-primary mb-2 mr-sm-2">Применить</button>
                    <a href="{{ route($currentRoute, ["collection" => $collection]) }}" class="btn btn-secondary mb-2">Сбросить</a>
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