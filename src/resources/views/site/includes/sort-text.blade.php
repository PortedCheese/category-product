@if (! empty($ico))
    <svg class="product-switch-sort__ico">
        <use xlink:href="#{{ $ico }}-{{ $arrow ?? "none" }}"></use>
    </svg>
@endif
<span class="product-switch-sort__title">
    {{ $title ?? "Сортировка" }}
</span>