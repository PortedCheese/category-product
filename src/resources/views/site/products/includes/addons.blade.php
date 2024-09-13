@if (count($addonsArray) >0)
    <div class="col-12 mt-3 order-last">
        <div class="product-show__addons" id="addonsGroup">
            <p class="product-show__addons-comment">Добавьте к заказу:</p>
            @foreach ($addonsArray as $key => $values)
                <button class="btn btn-outline-secondary product-show__addon-type collapsed me-2 mb-2"
                   data-bs-toggle="collapse"
                   data-bs-target="#collapseAddonType{{ $loop->index }}"
                   role="button"
                   aria-expanded="false"
                   aria-controls="collapseAddonType{{ $loop->index }}">
                    {{ $key }}
                </button>
            @endforeach
            @foreach ($addonsArray as $key => $values)
                <div class="collapse product-show__addons-collapse" id="collapseAddonType{{ $loop->index }}"  data-parent="#addonsGroup">
                    @foreach ($values as $addon)
                           @include("category-product::site.products.includes.addon-teaser")
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endif
