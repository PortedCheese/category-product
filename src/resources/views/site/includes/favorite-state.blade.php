<li class="nav-item">
    <favorite-state favorite-url="{{ route("catalog.favorite.index") }}"
                :favorite-items="{{ json_encode($items) }}">
    </favorite-state>
</li>