<div class="card-body">
    @can("update", \App\AddonType::class)
        <universal-priority :elements="{{ json_encode($types) }}"
                            :url="'{{ route("admin.vue.priority",['table' => "addon_types", "field" => "priority"]) }}'">
        </universal-priority>
    @else
        <ul>
            @foreach ($types as $type)
                <li>
                    @can("view", \App\AddonType::class)
                        <a href="{{ route('admin.product-collections.show', ['collection' => $type["slug"]]) }}"
                           class="btn btn-link">
                            {{ $type["title"] }}
                        </a>
                    @else
                        <span>{{ $type['title'] }}</span>
                    @endcan
                </li>
            @endforeach
        </ul>
    @endcan
</div>
