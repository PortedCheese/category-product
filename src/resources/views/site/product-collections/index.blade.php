@extends("layouts.boot")

@section('page-title')
    {{ config("category-product.productCollectionsName") }} -
@endsection

@section("header-title")
    {{ config("category-product.productCollectionsName") }}
@endsection

@section("contents")
    <div class="row">
        @foreach ($products as $item)
            <div class="col-12 col-sm-6 col-md-4 col-xl-3 category-teaser-cover">
                @include("category-product::site.product-collections.includes.teaser", ["collection" => $item])
            </div>
        @endforeach
    </div>
@endsection