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

@section("contents")
    <div class="row">
        @foreach ($categories as $item)
            <div class="col-12 col-sm-6 col-md-4 col-xl-3 category-teaser-cover">
                @include("category-product::site.categories.includes.teaser", ["category" => $item])
            </div>
        @endforeach
    </div>
@endsection