@extends("layouts.boot")

@section('page-title', "Каталог - ")

@section("header-title", "Каталог")

@section("contents")
    <div class="row">
        @foreach ($categories as $item)
            @include("category-product::site.categories.includes.teaser", ["category" => $item])
        @endforeach
    </div>
@endsection