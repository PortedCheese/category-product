@extends("layouts.boot")

@section('page-title', "{$product->title} - ")

@section("contents")
    <div class="row product-show">
        <div class="col-12">
            <div class="product-show__cover">
                    @if (config("category-product.useAddons"))
                        @includeFirst(["category-product::site.products.includes.addons",])
                    @endif
                    @include("category-product::site.products.includes.show-top-section")
            </div>
        </div>
        <div class="col-12 col-lg-8">
            <ul class="nav" id="productTab" role="tablist">
                <li class="nav-item">
                    <a id="about-tab" href="#about-text" class="nav-link active" data-toggle="tab" aria-selected="true">О товаре</a>
                </li>
                @if (! empty($groups))
                    <li class="nav-item">
                        <a id="spec-tab" href="#spec-text" class="nav-link" data-toggle="tab" aria-selected="false">Характеристики</a>
                    </li>
                @endif
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="about-text" role="tabpanel" aria-labelledby="about-tab">
                    <div class="description product-show__description">
                        {!! $product->description !!}
                    </div>
                </div>
                @if (! empty($groups))
                    <div class="tab-pane" id="spec-text" role="tabpanel" aria-labelledby="spec-tab">
                        <div class="table-responsive">
                            @foreach ($groups as $group)
                                <table class="table table-hover table-sm">
                                    @if ($group->model)
                                        <thead>
                                        <tr>
                                            <th colspan="2" class="border-0">
                                                <h5>{{ $group->title }}</h5>
                                            </th>
                                        </tr>
                                        </thead>
                                    @endif
                                    <tbody>
                                    @foreach ($group->specifications as $spec)
                                        <tr>
                                            <td class="w-25 border-0">
                                                {{ $spec->title }}
                                            </td>
                                            <td class="w-75 border-0 text-right">
                                                {{ implode(", ", $spec->values) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @if (! empty($watch))
        <div class="row mb-3">
            <div class="col-12">
                <h2>Вы смотрели</h2>
            </div>
        </div>
        <div class="row">
            @foreach ($watch as $item)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 products-grid-col">
                    @include("category-product::site.products.includes.teaser", ["product" => $item->getTeaserData()])
                </div>
            @endforeach
        </div>
    @endif
@endsection