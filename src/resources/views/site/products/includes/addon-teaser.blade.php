<div class="card product-show__addon-card product-show__addon-bg mb-3">
    <div class="row g-0">
        <div class="col-12 col-md-9">
            <div class="card-body p-2">
                @isset($addon->cover)
                    <div class="float-left me-3 mb-3">
                        @imgLazy([
                        "image" => $addon->cover,
                        "template" => "small",
                        "lightbox" => "addonLightGroup".$addon->id,
                        "grid" => [],
                        "imgClass" => "rounded",
                        ])
                    </div>
                @endisset
                <h3 class="card-title product-show__addon-title">{{ $addon->title }}</h3>
                @isset($addon->short)
                    <p class="card-text">
                        <small class="text-muted">
                            {{ $addon->short }}
                        </small>
                    </p>
                @endisset
                @isset($addon->description)
                    <div class="card-text text-muted product-show__addon-text">
                        {!!  $addon->description !!}
                    </div>
                @endisset
                @if ((! class_exists(\App\ProductVariation::class) || ! config("product-variation.enableVariations")) && ! empty($addonGroups = \PortedCheese\CategoryProduct\Facades\ProductActions::getProductSpecificationsByGroups($addon)))
                    <div class="tab-pane product-show__addon-specifications" id="addon-spec-text" role="tabpanel" aria-labelledby="addon-spec-tab">
                        <div class="table-responsive">
                            @foreach ($addonGroups as $group)
                                <table class="table table-hover table-sm">
                                    @if ($group->model)
                                        <thead>
                                        <tr>
                                            <th colspan="2" class="">
                                                <h5>{{ $group->title }}</h5>
                                            </th>
                                        </tr>
                                        </thead>
                                    @endif
                                    <tbody>
                                    @foreach ($group->specifications as $spec)
                                        <tr>
                                            <td class="w-25">
                                                {{ $spec->title }}
                                            </td>
                                            <td class="w-75 text-right">
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
        @if ($addon->variations)
            <div class="col-12 col-md-4 col-lg-3">
                <div class="card-body p-2">
                    @includeIf("product-variation::site.variations.addon-price",["product" => $addon, "parent" => $product])
                </div>
            </div>
        @endif

    </div>
</div>
