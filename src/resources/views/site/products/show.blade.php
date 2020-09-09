@extends("layouts.boot")

@section('page-title', "{$product->title} - ")

@section("contents")
    <div class="row product-show">
        <div class="col-12">
            <div class="product-show__cover">
                @include("category-product::site.products.includes.show-top-section")
            </div>
        </div>
    </div>
@endsection