<div class="card">
    <div class="card-body">
        <a href="{{ route("catalog.products.show", ["product" => $product]) }}">
            {{ $product->title }}
        </a>
    </div>
</div>