@extends("admin.layout")

@section("page-title", "Редактировать {$label->title} - ")

@section('header-title', "Редактировать {$label->title}")

@section('admin')
    @include("category-product::admin.product-labels.includes.pills")
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route("admin.product-labels.update", ["label" => $label]) }}" method="post">
                    @csrf
                    @method("put")

                    <div class="form-group">
                        <label for="title">Заголовок <span class="text-danger">*</span></label>
                        <input type="text"
                               id="title"
                               name="title"
                               required
                               value="{{ old("title", $label->title) }}"
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
                               value="{{ old("slug", $label->slug) }}"
                               class="form-control @error("slug") is-invalid @enderror">
                        @error("slug")
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="color">Цвет метки <span class="text-danger">*</span></label>
                        <select name="color"
                                id="color"
                                required
                                class="form-control custom-select @error("color") is-invalid @enderror">
                            <option value="">Выберите...</option>
                            @foreach($colors as $item)
                                <option value="{{ $item->key }}"
                                        {{ old("color", $label->color) == $item->key ? "selected" : "" }}>
                                    {{ $item->title }}
                                </option>
                            @endforeach
                        </select>
                        @error("color")
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="my-2">
                            @foreach ($colors as $item)
                                <div class="product-label product-label_{{ $item->key }}">
                                    {{ $item->title }}
                                </div>
                            @endforeach
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