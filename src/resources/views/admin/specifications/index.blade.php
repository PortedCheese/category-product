@extends("admin.layout")

@section("page-title", "Характеристики {$category->title} - ")

@section('header-title', "Характеристики {$category->title}")

@section('admin')
    @include("category-product::admin.specifications.includes.pills")
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Заголовок</th>
                            <th>Тип</th>
                            <th>В фильтре</th>
                            <th>Группа</th>
                            <th>Приоритет</th>
                            @canany(["update", "delete"], \App\CategoryField::class)
                                <th>Действия</th>
                            @endcan
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($specifications as $item)
                            <tr>
                                <td>{{ $item->pivot->title }}</td>
                                <td>{{ $item->type }}</td>
                                <td>
                                    {{ $item->pivot->filter ? "Да" : "Нет" }}
                                </td>
                                <td>
                                    @if (! empty($item->group_id))
                                        {{ $item->group->title }}
                                    @else
                                        Не задана
                                    @endif
                                </td>
                                <td>
                                    {{ $item->pivot->priority }}
                                </td>
                                @canany(["update", "delete"], $item)
                                    <td>
                                        <div role="toolbar" class="btn-toolbar">
                                            <div class="btn-group mr-1">
                                                @can("update", $item)
                                                    <a href="{{ route("admin.categories.specifications.edit", ["category" => $category, "specification" => $item]) }}" class="btn btn-primary">
                                                        <i class="far fa-edit"></i>
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
                                                    <form action="{{ route('admin.categories.specifications.destroy', ["category" => $category, 'specification' => $item]) }}"
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
@endsection