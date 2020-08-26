@extends("layouts.boot")

@section('page-title', "{$category->title} - ")

@section("header-title", "{$category->title}")

@section("sidebar")
    <div class="row">
        <div class="col-12">
            aside
        </div>
    </div>
@endsection

@section("contents")
    @if ($categories->count())
        @include("category-product::site.categories.includes.children", ["children" => $categories])
    @endif

    @include("category-product::site.products.includes.sort-view", ["productView" => $productView])

    @include("category-product::site.products.includes.grid", ["products" => $products, "productView" => $productView])
@endsection

@push("more-scripts")
    <script>
        let cookieUrl = "{{ route('catalog.categories.product-view') }}";
    </script>
@endpush