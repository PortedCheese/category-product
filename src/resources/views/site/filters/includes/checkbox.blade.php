<catalog-filter-checkbox :filter-data="{{ json_encode($filter) }}"
                         :limit="{{ config("category-product.defaultCheckboxLimit") }}"
                         @if (isset($modal)) :modal="true" @endif>
</catalog-filter-checkbox>