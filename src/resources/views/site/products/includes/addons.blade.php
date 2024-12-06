@if (count($addonsArray) >0)
    <div class="col-12 mt-3 order-last">
        <p class="product-show__addons-comment">Добавьте к заказу:</p>
        <div class="product-show__addons" id="addonsGroup">
            <div class="nav nav-pills"  role="tablist">
                @foreach ($addonsArray as $key => $values)
                    <li class="nav-item" role="presentation">
                        <button class="btn btn-outline-secondary product-show__addon-type me-2 mb-2"
                                id="pillsAddonType{{ $loop->index }}"
                                data-bs-toggle="pill"
                                data-bs-target="#collapseAddonType{{ $loop->index }}"
                                type="button" role="tab"
                                aria-selected="false"
                                aria-controls="collapseAddonType{{ $loop->index }}">
                            {{ $key }}
                        </button>
                    </li>
                @endforeach
            </div>
            <div class="tab-content">
                @foreach ($addonsArray as $key => $values)
                    <div class="tab-pane fade product-show__addons-collapse" id="collapseAddonType{{ $loop->index }}"
                         role="tabpanel"
                         aria-labelledby="pillsAddonType{{ $loop->index }}">
                        @foreach ($values as $addon)
                            @include("category-product::site.products.includes.addon-teaser")
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
