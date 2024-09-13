@extends("admin.layout")

@section("page-title", "{$product->title} - ")

@section('header-title', "{$product->title}")

@section('admin')
    @include("category-product::admin.products.includes.pills")
    @includeIf("product-variation::admin.product-variations.includes.list")
    <div class="col-12">
        <div class="card">
            @can("changeCategory", $product)
                <div class="card-header">
                    <button type="button" class="btn btn-warning collapse show" data-bs-toggle="collapse" data-bs-target="#changeCategoryAlert">
                        Изменить категорию
                    </button>
                    <div class="collapse collapseChangeCategory" id="changeCategoryAlert">
                        При изменении категории, характеристики, которые отсутствуют в целевой категории, будут добавлены.
                        <button id="changeCategoryCancelBtn" type="button" class="btn btn-secondary"
                                data-bs-toggle="collapse"
                                data-bs-target="#changeCategoryAlert">
                            Отмена
                        </button>
                        <button type="button"
                                class="btn btn-primary"
                                onclick="this.style.display='none';document.querySelector('#changeCategoryCancelBtn').style.display='none'"
                                data-bs-toggle="collapse"
                                data-bs-target=".collapseChangeCategory"
                                aria-expanded="false"
                                aria-controls="collapseChangeCategory">
                            Понятно
                        </button>
                    </div>
                </div>
                    <div class="collapse mt-3 collapseChangeCategory">
                        <form class="form-inline"
                              method="post"
                              action="{{ route("admin.products.change-category", ['product' => $product]) }}">
                            @csrf
                            @method('put')
                            <div class="form-group">
                                <label for="category_id" class="sr-only">Категория</label>
                                <div class="input-group">
                                    <select name="category_id"
                                            id="category_id"
                                            class="custom-select">
                                        @foreach($categories as $key => $value)
                                            <option value="{{ $key }}"
                                                    @if ($key == $category->id)
                                                    selected
                                                    @elseif (old('category_id'))
                                                    selected
                                                    @endif>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-success" type="submit">Обновить</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endcan
            <div class="card-body">
                <dl class="row">
                    @if ($product->short)
                        <dt class="col-sm-3">Краткое описание:</dt>
                        <dd class="col-sm-9">
                            {{ $product->short }}
                        </dd>
                    @endif
                    @if ($product->description)
                        <dt class="col-sm-3">Описание:</dt>
                        <dd class="col-sm-9">
                            <div>{!! $product->description !!}</div>
                        </dd>
                    @endif
                    @if ($labels->count())
                        <dt class="col-sm-3">Метки:</dt>
                        <dd class="col-sm-9">
                            <ul class="list-unstyled">
                                @foreach ($labels as $label)
                                    <li>
                                        <a href="{{ route("admin.product-labels.show", ["label" => $label]) }}"
                                           target="_blank">
                                           {{ $label->title }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </dd>
                    @endif
                    @if ($collections->count())
                        <dt class="col-sm-3">{{ config("category-product.productCollectionsName") }}:</dt>
                        <dd class="col-sm-9">
                            <ul class="list-unstyled rounded bg-light p-3">
                                @foreach ($collections as $collection)
                                    <li>
                                        <a href="{{ route("admin.product-collections.show", ["collection" => $collection]) }}"
                                           target="_blank">
                                           {{ $collection->title }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>


@endsection