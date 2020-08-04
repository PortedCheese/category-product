@extends("admin.layout")

@section("page-title", "Добавить метку товара - ")

@section('header-title', "Добавить метку товара")

@section('admin')
    @include("category-product::admin.product-labels.includes.pills")
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route("admin.product-labels.store") }}" method="post">
                    @csrf

                    <div class="form-group">
                        <label for="title">Заголовок <span class="text-danger">*</span></label>
                        <input type="text"
                               id="title"
                               name="title"
                               required
                               value="{{ old('title') }}"
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
                               value="{{ old('slug') }}"
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
                                        {{ old("color") == $item->key ? "selected" : "" }}>
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
                        <button type="submit" class="btn btn-success">Добавить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection