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
    <div class="row">
        @if ($categories->count())
            @include("category-product::site.categories.includes.children", ["children" => $categories])
        @endif
        <div class="col-12">
            test
        </div>
    </div>
@endsection