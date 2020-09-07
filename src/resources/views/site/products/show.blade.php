@extends("layouts.boot")

@section('page-title', "{$product->title} - ")

@section("contents")
    <div class="row">
        <div class="col-12 col-md-6">
            Image
        </div>
        <div class="col-12 col-md-6">
            <h1>{{ $product->title }}</h1>
            @includeFirst([
                    "variation-cart::site.variations.show",
                    "product-variation::site.variations.show",
                    "category-product::site.products.includes.short",
                ], ["product" => $product])
        </div>
    </div>
@endsection