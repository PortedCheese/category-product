@extends("admin.layout")

@section("page-title", "Доступные характеристики - ")

@section('header-title', "Доступные характеристики")

@section('admin')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <form action="{{ route($currentRoute) }}" method="get" class="form-inline">
                    <label class="sr-only" for="title">Заголовок</label>
                    <input type="text"
                           class="form-control mb-2 mr-sm-2"
                           id="title"
                           placeholder="Заголовок"
                           value="{{ $request->get("title", "") }}"
                           name="title">

                    <button type="submit" class="btn btn-primary mb-2 mr-sm-2">Поиск</button>
                    <a href="{{ route($currentRoute) }}" class="btn btn-outline-secondary mb-2">Сбросить</a>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Заголовок</th>
                            <th>Тип</th>
                            <th>Группа</th>
                            @can("view", \App\Specification::class)
                                <th>Действия</th>
                            @endcan
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($specifications as $item)
                            <tr>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->type_human }}</td>
                                <td>
                                    @if (! empty($item->group_id))
                                        {{ $item->group->title }}
                                    @else
                                        Не задана
                                    @endif
                                </td>
                                @can("view", $item)
                                    <td>
                                        <a href="{{ route('admin.specifications.show', ['specification' => $item]) }}" class="btn btn-dark">
                                            <i class="far fa-eye"></i>
                                        </a>
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if ($specifications->lastPage() > 1)
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{ $specifications->links() }}
                </div>
            </div>
        </div>
    @endif
@endsection