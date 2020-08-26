<div class="btn-group dropright product-switch-sort">
    <button type="button" class="btn product-switch-sort__btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        @if (! empty($sortOptions["{$sortBy}.{$sortDirection}"]))
            @php($item = $sortOptions["{$sortBy}.{$sortDirection}"])
            @sortText(["title" => $item->title, "ico" => $item->ico, "arrow" => $item->arrow])
        @else
            @sortText()
        @endif
    </button>
    <div class="dropdown-menu">
        @foreach ($sortOptions as $item)
            @sortLink([
                "title" => $item->title,
                "ico" => $item->ico,
                "arrow" => $item->arrow,
                "sortBy" => $item->by,
                "sortDirection" => $item->direction
            ])
        @endforeach
    </div>
</div>