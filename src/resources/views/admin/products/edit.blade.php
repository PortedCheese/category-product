@extends("admin.layout")

@section("page-title", "{$product->title} - ")

@section('header-title', "{$product->title}")

@section('admin')
    @include("category-product::admin.products.includes.pills")
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route("admin.products.update", ["product" => $product]) }}" method="post">
                    @csrf
                    @method("put")

                    <div class="form-group">
                        <label for="title">Заголовок <span class="text-danger">*</span></label>
                        <input type="text"
                               id="title"
                               name="title"
                               maxlength="100"
                               required
                               value="{{ old("title", $product->title) }}"
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
                               maxlength="100"
                               value="{{ old("slug", $product->slug) }}"
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
                               maxlength="150"
                               name="short"
                               value="{{ old("short", $product->short) }}"
                               class="form-control @error("short") is-invalid @enderror">
                        @error("short")
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Описание</label>
                        <textarea class="form-control tiny @error("description") is-invalid @enderror"
                                  name="description"
                                  id="description"
                                  rows="3">{{ old('description') ? old('description') : $product->description }}</textarea>
                        @error("description")
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Метки</label>
                        @foreach($labels as $label)
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input"
                                       type="checkbox"
                                       {{ (! count($errors->all()) && in_array($label->id, $currentLabels)) || in_array($label->id, old("labels[]", [])) ? "checked" : "" }}
                                       value="{{ $label->id }}"
                                       id="check-{{ $label->id }}"
                                       name="labels[]">
                                <label class="custom-control-label" for="check-{{ $label->id }}">
                                    {{ $label->title }}
                                </label>
                            </div>
                        @endforeach
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