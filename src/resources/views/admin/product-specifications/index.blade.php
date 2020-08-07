@extends("admin.layout")

@section("page-title", "{$product->title} - ")

@section('header-title', "{$product->title}")

@section('admin')
    @include("category-product::admin.products.includes.pills")
    <admin-product-specifications
            get-url="{{ route("admin.products.specifications.current", ["product" => $product]) }}"
            post-url="{{ route("admin.products.specifications.store", ["product" => $product]) }}">
    </admin-product-specifications>
@endsection