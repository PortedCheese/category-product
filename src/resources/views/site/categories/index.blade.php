@extends("layouts.boot")

@section('page-title')
    @empty($category)
        Каталог -
    @else
        {{ $category->title }} -
    @endempty
@endsection

@section("header-title")
    @empty($category)
        Каталог
    @else
        {{ $category->title }}
    @endempty
@endsection

@push("svg")
    @include("category-product::site.includes.svg")
@endpush

@section("contents")
    <div class="row">
        @foreach ($categories as $item)
            @include("category-product::site.categories.includes.teaser", ["category" => $item])
        @endforeach
    </div>
@endsection