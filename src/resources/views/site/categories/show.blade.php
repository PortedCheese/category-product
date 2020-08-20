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

    <div class="row justify-content-between">
        <div class="col">Сортировка</div>
        <div class="col">
            <div class="btn-group float-right" id="catalog-switcher" role="group" aria-label="Basic example">
                <button type="button" class="btn btn-primary list">List</button>
                <button type="button" class="btn btn-primary bar">Bar</button>
            </div>
        </div>
    </div>

    <div id="product-grid" class="row products-grid{{ $productView == "list" ? " products-grid_list" : "" }}">
        @foreach ($products as $item)
            <div class="col-12 col-sm-6 col-md-4 products-grid-col">
                @include("category-product::site.products.includes.teaser", ["product" => $item])
            </div>
        @endforeach
    </div>
@endsection

@push("more-scripts")
    <script>
        let cookieUrl = "{{ route('catalog.categories.product-view') }}";
    </script>
@endpush