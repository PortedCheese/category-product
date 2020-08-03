@extends("admin.layout")

@section("page-title", "Категории - ")

@section('header-title', "Категории")

@section('admin')
    @include("category-product::admin.categories.includes.pills")
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if ($isTree)
                    @include("category-product::admin.categories.includes.tree", ["categories" => $categories])
                @else
                    @include("category-product::admin.categories.includes.table-list", ["categories" => $categories])
                @endif
            </div>
        </div>
    </div>
@endsection