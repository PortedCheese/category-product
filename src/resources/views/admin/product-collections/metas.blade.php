@extends("admin.layout")

@section("page-title", "{$collection->title} - ")

@section('header-title', "{$collection->title}")

@section('admin')
    @include("category-product::admin.product-collections.includes.pills")
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Добавить тег</h5>
            </div>
            <div class="card-body">
                @include("seo-integration::admin.meta.create", ['model' => 'product_collection', 'id' => $collection->id])
            </div>
        </div>
    </div>
    <div class="col-12 mt-2">
        <div class="card">
            <div class="card-body">
                @include("seo-integration::admin.meta.table-models", ['metas' => $collection->metas])
            </div>
        </div>
    </div>
@endsection