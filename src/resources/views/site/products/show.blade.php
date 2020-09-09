@extends("layouts.boot")

@section('page-title', "{$product->title} - ")

@section("contents")
    <div class="row product-show">
        <div class="col-12">
            <div class="product-show__cover">
                @include("category-product::site.products.includes.show-top-section")
            </div>
        </div>
        <div class="col-12">
            <ul class="nav" id="productTab" role="tablist">
                <li class="nav-item">
                    <a id="about-tab" href="#about-text" class="nav-link active" data-toggle="tab" aria-selected="true">О товаре</a>
                </li>
                <li class="nav-item">
                    <a id="spec-tab" href="#spec-text" class="nav-link" data-toggle="tab" aria-selected="false">Характеристики</a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="about-text" role="tabpanel" aria-labelledby="about-tab">
                    {!! $product->description !!}
                </div>
                <div class="tab-pane" id="spec-text" role="tabpanel" aria-labelledby="spec-tab">
                    Spec
                </div>
            </div>
        </div>
    </div>
@endsection