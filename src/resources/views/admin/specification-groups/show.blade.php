@extends("admin.layout")

@section("page-title", "Просмотр {$group->title} - ")

@section('header-title', "Просмотр {$group->title}")

@section('admin')
    @include("category-product::admin.specification-groups.includes.pills")
    <div class="col-12">
        <div class="card">
            @can("update", $group)
                <div class="card-header">
                    <form action="{{ route("admin.specification-groups.update", ["group" => $group]) }}" method="post" class="form-inline">
                        @csrf
                        @method("put")

                        <div class="form-group mr-sm-3 mb-2">
                            <label for="title" class="sr-only">Заголовок <span class="text-danger">*</span></label>
                            <input type="text"
                                   id="title"
                                   name="title"
                                   required
                                   placeholder="Заголовок"
                                   value="{{ old("title", $group->title) }}"
                                   class="form-control @error("title") is-invalid @enderror">
                        </div>

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
                </div>
            @endcan
            <div class="card-body">
                <h5>Поля относящиеся к группе</h5>
                <ul>
                    @foreach ($specifications as $item)
                        <li>
                            <a href="{{ route("admin.specifications.show", ["specification" => $item]) }}">
                                {{ $item->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection