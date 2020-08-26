<a href="{{ $sortUrl }}{{ $noParams ? "?" : "&" }}sort-by={{ $sortBy }}&sort-order={{ $sortDirection }}"
   class="{{ $class ?? "dropdown-item" }}">
    @sortText(["title" => $title ?? "Сортировка", "ico" => $ico ?? "", "arrow" => $arrow ?? ""])
</a>