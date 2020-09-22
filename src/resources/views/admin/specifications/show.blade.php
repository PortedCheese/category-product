@extends("admin.layout")

@section("page-title", "Просмотр {$specification->title} - ")

@section('header-title', "Просмотр {$specification->title}")

@section('admin')
    <div class="col-12">
        <div class="card">
            @can("update", $specification)
                <div class="card-header">
                    <form action="{{ route("admin.specifications.update", ["specification" => $specification]) }}" class="form-inline" method="post">
                        @csrf
                        @method("put")

                        <div class="form-group mr-sm-2 mb-2">
                            <label for="title" class="sr-only">Заголвок <span class="text-danger">*</span></label>
                            <input type="text"
                                   id="title"
                                   name="title"
                                   required
                                   value="{{ old("title", $specification->title) }}"
                                   class="form-control @error("title") is-invalid @enderror">
                        </div>

                        <div class="form-group mr-sm-2 mb-2">
                            <label for="type" class="sr-only">Виджет поля</label>
                            <select name="type"
                                    id="type"
                                    class="form-control custom-select @error("type") is-invalid @enderror">
                                <option value="">Выберите...</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->key }}"
                                            {{ old("type", $specification->type) == $type->key ? "selected" : "" }}>
                                        {{ $type->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if ($groups->count())
                            <div class="form-group mr-sm-2 mb-2">
                                <label for="group_id" class="sr-only">Группа</label>
                                <select name="group_id"
                                        id="group_id"
                                        class="form-control custom-select @error("group_id") is-invalid @enderror">
                                    <option value="">Выберите...</option>
                                    @foreach($groups as $item)
                                        <option value="{{ $item->id }}"
                                                {{ old("group_id", $specification->group_id) == $item->id ? "selected" : "" }}>
                                            {{ $item->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="btn-group mb-2"
                             role="group">
                            <button type="submit" class="btn btn-success">Обновить</button>
                        </div>
                    </form>

                    @error("title")
                        <input type="hidden" class="form-control is-invalid">
                        <div class="invalid-feedback" role="alert">
                            {{ $message }}
                        </div>
                    @enderror
                    @error("type")
                        <input type="hidden" class="form-control is-invalid">
                        <div class="invalid-feedback" role="alert">
                            {{ $message }}
                        </div>
                    @enderror
                    @error("group_id")
                        <input type="hidden" class="form-control is-invalid">
                        <div class="invalid-feedback" role="alert">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            @endcan
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Название</dt>
                    <dd class="col-sm-9">{{ $specification->title }}</dd>

                    <dt class="col-sm-3">Тип</dt>
                    <dd class="col-sm-9">{{ $specification->type_human }}</dd>

                    <dt class="col-sm-3">Группа</dt>
                    <dd class="col-sm-9">{{ empty($group) ? "Не задана" : $group->title }}</dd>
                </dl>
            </div>
            @can("view", \App\Category::class)
                <div class="card-footer">
                    <h3>Категории, в которых используется характеристика</h3>
                    <ul>
                        @foreach($categories as $category)
                            <li>
                                <a href="{{ route('admin.categories.specifications.index', ['category' => $category]) }}">
                                    {{ $category->title }} ({{ $category->pivot->title }})
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    @can('update', $specification)
                        <button type="button" class="btn btn-warning" data-confirm="{{ "sync-form-{$specification->id}" }}">
                            Синхронизировать заголовки
                        </button>
                        <confirm-form :id="'{{ "sync-form-{$specification->id}" }}'" confirm-text="Да, синхронизировать!">
                            <template>
                                <form action="{{ route('admin.specifications.sync', ["specification" => $specification]) }}"
                                      id="sync-form-{{ $specification->id }}"
                                      class="btn-group"
                                      method="post">
                                    @csrf
                                    @method("put")
                                </form>
                            </template>
                        </confirm-form>
                    @endcan
                </div>
            @endcan
        </div>
    </div>
@endsection