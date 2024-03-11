@extends("admin.layout")

@section("page-title", config("category-product.productCollectionsName")." товаров - ")

@section('header-title', config("category-product.productCollectionsName")." товаров")

@section('admin')
    @include("category-product::admin.product-collections.includes.pills")
    <div class="col-12">
        <div class="card">
            @if ($isTree)
                @include("category-product::admin.product-collections.includes.tree", ["collections" => $collections])
            @else
                @include("category-product::admin.product-collections.includes.table-list", ["collections" => $collections])
            @endif
        </div>
    </div>
@endsection