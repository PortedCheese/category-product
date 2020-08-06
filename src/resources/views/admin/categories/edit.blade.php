@extends("admin.layout")

@section("page-title", "{$category->title} - ")

@section('header-title', "{$category->title}")

@section('admin')
    @include("category-product::admin.categories.includes.pills")
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route("admin.categories.update", ["category" => $category]) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method("put")

                    <div class="form-group">
                        <label for="title">Заголовок <span class="text-danger">*</span></label>
                        <input type="text"
                               id="title"
                               name="title"
                               required
                               value="{{ old("title", $category->title) }}"
                               class="form-control @error("title") is-invalid @enderror">
                        @error("title")
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="slug">Адресная строка</label>
                        <input type="text"
                               id="slug"
                               name="slug"
                               value="{{ old("slug", $category->slug) }}"
                               class="form-control @error("slug") is-invalid @enderror">
                        @error("slug")
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="short">Краткое описание</label>
                        <input type="text"
                               id="short"
                               name="short"
                               value="{{ old("short", $category->short) }}"
                               class="form-control @error("short") is-invalid @enderror">
                        @error("short")
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="custom-file-input">Изображение</label>
                        <div class="custom-file">
                            <input type="file"
                                   class="custom-file-input{{ $errors->has('iamge') ? ' is-invalid' : '' }}"
                                   id="custom-file-input"
                                   lang="ru"
                                   name="iamge"
                                   aria-describedby="inputGroupImage">
                            <label class="custom-file-label"
                                   for="custom-file-input">
                                Выберите файл
                            </label>
                            @if ($errors->has('iamge'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('iamge') }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="btn-group"
                         role="group">
                        <button type="submit" class="btn btn-success">Обновить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection