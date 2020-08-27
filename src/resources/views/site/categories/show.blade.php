@extends("layouts.boot")

@section('page-title', "{$category->title} - ")

@section("header-upper-title", "{$category->title}")

@if (! empty($filters))
    @section("sidebar")
        @include("category-product::site.filters.sidebar")
    @endsection
@endif

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