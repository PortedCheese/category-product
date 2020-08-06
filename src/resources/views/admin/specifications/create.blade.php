@extends("admin.layout")

@section("page-title", "{$category->title} - ")

@section('header-title', "{$category->title}")

@section('admin')
    @include("category-product::admin.specifications.includes.pills")
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route("admin.categories.specifications.store", ["category" => $category]) }}"
                      method="post">
                    @csrf
                    <input type="hidden" name="priority" value="{{ $nextSpec }}">

                    @if ($available->count())
                        <div class="form-group border border-primary p-2 bg-light rounded">
                            <label for="exists">Выбрать из существующих</label>
                            <select name="exists"
                                    id="exists"
                                    class="form-control custom-select @error("exists") is-invalid @enderror">
                                <option value="">Выберите...</option>
                                @foreach($available as $item)
                                    <option value="{{ $item->id }}"
                                            {{ old("exists") == $item->id ? "selected" : "" }}>
                                        {{ $item->title }} | {{ $item->type_human }}
                                        @if (! empty($item->group_id))
                                            ({{ $item->group->title }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error("exists")
                                <div class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="title">Заголовок</label>
                        <input type="text"
                               id="title"
                               name="title"
                               value="{{ old('title') }}"
                               class="form-control @error("title") is-invalid @enderror">
                        @error("title")
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    @can("settings-management")
                        <div class="form-group">
                            <label for="slug">Ключ</label>
                            <input type="text"
                                   id="slug"
                                   name="slug"
                                   value="{{ old('slug') }}"
                                   class="form-control @error("slug") is-invalid @enderror">
                            @error("slug")
                                <div class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    @endcan

                    <div class="form-group">
                        <label for="type">Виджет поля</label>
                        <select name="type"
                                id="type"
                                class="form-control custom-select @error("type") is-invalid @enderror">
                            <option value="">Выберите...</option>
                            @foreach($types as $item)
                                <option value="{{ $item->key }}"
                                        {{ old("type") == $item->key ? "selected" : "" }}>
                                    {{ $item->title }}
                                </option>
                            @endforeach
                        </select>
                        @error("type")
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    @if ($groups->count())
                        <div class="form-group">
                            <label for="group_id">Группа</label>
                            <select name="group_id"
                                    id="group_id"
                                    class="form-control custom-select @error("group_id") is-invalid @enderror">
                                <option value="">Выберите...</option>
                                @foreach($groups as $item)
                                    <option value="{{ $item->id }}"
                                            {{ old("group_id") == $item->id ? "selected" : "" }}>
                                        {{ $item->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error("group_id")
                                <div class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    @endif

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="filter"
                                   {{ old("filter") ? "checked" : "" }}
                                   name="filter">
                            <label class="custom-control-label" for="filter">Добавить в фильтр</label>
                        </div>
                    </div>

                    <div class="btn-group"
                         role="group">
                        <button type="submit" class="btn btn-success">Добавить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection