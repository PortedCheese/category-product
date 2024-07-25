@extends("admin.layout")

@section("page-title", "Просмотр {$type->title} - ")

@section('header-title', "Просмотр {$type->title}")

@section('admin')
    @include("category-product::admin.addon-types.includes.pills")
    @if ($type->short || $type->description)
        <div class="col-12 mb-2">
            <div class="card">
                <div class="card-body">
                    <dl class="row">
                        @if ($type->short)
                            <dt class="col-sm-3">Краткое описание:</dt>
                            <dd class="col-sm-9">
                                {{ $type->short }}
                            </dd>
                        @endif
                        @if ($type->description)
                            <dt class="col-sm-3">Описание:</dt>
                            <dd class="col-sm-9">
                                <div>{!! $type->description !!}</div>
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
                <p><b>{{ config("category-product.addonsName") }}:</b></p>
                <form action="{{ route($currentRoute, ["type" => $type]) }}" method="get" class="form-inline">
                    <label for="title" class="sr-only">Заголовок</label>
                    <input type="text"
                           id="title"
                           placeholder="Заголовок"
                           name="title"
                           value="{{ $request->get("title", "") }}"
                           class="form-control mb-2 mr-sm-2">

                    <button type="submit" class="btn btn-primary mb-2 mr-sm-2">Применить</button>
                    <a href="{{ route($currentRoute, ["type" => $type]) }}" class="btn btn-secondary mb-2">Сбросить</a>
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
                        @foreach ($addons as $addon)
                            <tr>
                                <td>{{ $addon->title }}</td>
                                <td>
                                    <a href="{{ route('admin.categories.show', ['category' => $addon->category]) }}">
                                        {{ $addon->category->title }}
                                    </a>
                                </td>
                                <td>
                                    <div role="toolbar" class="btn-toolbar">
                                        <div class="btn-group mr-1">
                                            @can("update", $addon)
                                                <a href="{{ route('admin.products.edit', ['product' => $addon]) }}"
                                                   class="btn btn-primary">
                                                    <i class="far fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can("view", $addon)
                                                <a href="{{ route('admin.products.show', ['product' => $addon]) }}"
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

    @if ($addons->LastPage() > 1)
        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-body">
                    {{ $addons->links() }}
                </div>
            </div>
        </div>
    @endif
@endsection