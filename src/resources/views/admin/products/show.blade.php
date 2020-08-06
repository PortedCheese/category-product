@extends("admin.layout")

@section("page-title", "{$product->title} - ")

@section('header-title', "{$product->title}")

@section('admin')
    @include("category-product::admin.products.includes.pills")
    <div class="col-12">
        <div class="card">
            @can("changeCategory", $product)
                <div class="card-header">
                    <button type="button" class="btn btn-warning">
                        Изменить категорию
                    </button>
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
                </dl>
            </div>
        </div>
    </div>
@endsection