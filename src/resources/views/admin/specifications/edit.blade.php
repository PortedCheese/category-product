@extends("admin.layout")

@section("page-title", "{$category->title} - ")

@section('header-title', "{$category->title}")

@section('admin')
    @include("category-product::admin.specifications.includes.pills")
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route("admin.categories.specifications.update", ["category" => $category, "specification" => $specification]) }}" method="post">
                    @csrf
                    @method("put")

                    <div class="form-group">
                        <label for="title">Заголовок <span class="text-danger">*</span></label>
                        <input type="text"
                               id="title"
                               name="title"
                               required
                               value="{{ old("title", $pivot->title) }}"
                               class="form-control @error("title") is-invalid @enderror">
                        @error("title")
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="priority">Приоритет <span class="text-danger">*</span></label>
                        <input type="number"
                               step="1"
                               min="1"
                               id="priority"
                               name="priority"
                               required
                               value="{{ old("priority", $pivot->priority) }}"
                               class="form-control @error("priority") is-invalid @enderror">
                        @error("priority")
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="filter"
                                   {{ (! count($errors->all()) && $pivot->filter) || old("filter") ? "checked" : "" }}
                                   name="filter">
                            <label class="custom-control-label" for="filter">Добавить в фильтр</label>
                        </div>
                    </div>

                    <div class="btn-group"
                         role="group">
                        <button type="submit" class="btn btn-success">Обновить</button>
                    </div>
                    <small class="form-text text-muted">
                        Группа меняется для всех полей <a target="_blank" href="{{ route("admin.specifications.show", ["specification" => $specification]) }}">этого</a> типа
                    </small>
                </form>
            </div>
        </div>
    </div>
@endsection