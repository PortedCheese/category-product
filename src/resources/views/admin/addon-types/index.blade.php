@extends("admin.layout")

@section("page-title", config("category-product.addonTypesName")." - ")

@section('header-title', config("category-product.addonTypesName")." ")

@section('admin')
    @include("category-product::admin.addon-types.includes.pills")
    <div class="col-12">
        <div class="card">
            @if ($isTree)
                @include("category-product::admin.addon-types.includes.tree", ["types" => $types])
            @else
                @include("category-product::admin.addon-types.includes.table-list", ["types" => $types])
            @endif
        </div>
    </div>
@endsection