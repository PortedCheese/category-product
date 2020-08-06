@extends("admin.layout")

@section("page-title", "Галерея - ")

@section('header-title', "{$product->title}")

@section('admin')
    @include("category-product::admin.products.includes.pills")
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <gallery csrf-token="{{ csrf_token() }}"
                         upload-url="{{ route('admin.vue.gallery.post', ['id' => $product->id, 'model' => 'products']) }}"
                         get-url="{{ route('admin.vue.gallery.get', ['id' => $product->id, 'model' => 'products']) }}">
                </gallery>
            </div>
        </div>
    </div>
@endsection