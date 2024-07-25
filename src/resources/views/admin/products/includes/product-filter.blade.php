@if(config("category-product.useAddons", false))
    <select class="custom-select mb-2 mr-sm-2" name="addon_type" aria-label="{{ config("category-product.addonTypesName") }}">
        <option value=""{{ ! $request->has('addon_type') || $request->get('addon_type') == '' ? " selected" : '' }}>
            Товары и дополнения
        </option>
        <option value="product"{{ $request->has('addon_type') || $request->get('addon_type') == 'product' ? " selected" : '' }}>
            Только товары
        </option>
        @foreach ($types as $type)
            <option value="{{ $type->id }}"{{ $request->get('addon_type') == $type->id ? " selected" : '' }}>
                {{ config("category-product.addonsName") }}: {{ $type->title }}
            </option>
        @endforeach
    </select>
@endif