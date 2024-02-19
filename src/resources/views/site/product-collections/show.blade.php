@extends("layouts.boot")

@section('page-title', "{$collection->title} - ")

@section("header-title", "{$collection->title}")

@section("contents")

    @isset($collection->short)
        <div class="row">
            <div class="col-12">
                <p class="text-muted">{{ $collection->short }}</p>
            </div>
        </div>
    @endisset

    <div class="row products-grid">
        @foreach ($products as $item)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 products-grid-col">
                @include("category-product::site.products.includes.teaser", ["product" => $item->getTeaserData()])
            </div>
        @endforeach
    </div>

    @if ($products->lastPage() > 1)
        <div class="row">
            <div class="col-12">
                {{ $products->links() }}
            </div>
        </div>
    @endif

    @isset($collection->short)
        <div class="row">
            <div class="col-12">
                {!! $collection->description !!}
            </div>
        </div>
    @endisset

@endsection