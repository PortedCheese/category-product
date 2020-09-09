<favorite-product add-url="{{ route("catalog.favorite.add-to-favorite", ["product" => $product]) }}"
                  remove-url="{{ route("catalog.favorite.remove-from-favorite", ["product" => $product]) }}"
                  :product-id="{{ $product->id }}"
                  :current="{{ json_encode($items) }}"
                  @if (isset($teaser)) :simple="true" @endif></favorite-product>