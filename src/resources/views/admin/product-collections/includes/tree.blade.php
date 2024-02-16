<div class="card-body">
    @can("update", \App\ProductCollection::class)
        <universal-priority :elements="{{ json_encode($collections) }}"
                            :url="'{{ route("admin.vue.priority",['table' => "product_collections", "field" => "priority"]) }}'">
        </universal-priority>
    @else
        <ul>
            @foreach ($collections as $collection)
                <li>
                    @can("view", \App\ProductCollection::class)
                        <a href="{{ route('admin.product-collections.show', ['collection' => $collection["slug"]]) }}"
                           class="btn btn-link">
                            {{ $collection["title"] }}
                        </a>
                    @else
                        <span>{{ $collection['title'] }}</span>
                    @endcan
                </li>
            @endforeach
        </ul>
    @endcan
</div>
