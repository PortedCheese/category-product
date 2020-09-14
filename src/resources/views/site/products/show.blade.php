@extends("layouts.boot")

@section('page-title', "{$product->title} - ")

@section("contents")
    <div class="row product-show">
        <div class="col-12">
            <div class="product-show__cover">
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
@endsection