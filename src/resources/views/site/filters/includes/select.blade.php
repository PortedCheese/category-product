<div class="form-group">
    <label for="{{ $filter->slug }}{{ isset($modal) ?: "-sidebar" }}">{{ $filter->title }}</label>
    <select name="select-{{ $filter->slug }}"
            id="{{ $filter->slug }}{{ isset($modal) ?: "-sidebar" }}"
            class="form-control custom-select">
        <option value="">Выберите...</option>
        @foreach($filter->values as $value)
            <option value="{{ $value }}"
                    {{ $request->get("select-{$filter->slug}", "") == $value ? "selected" : "" }}>
                {{ $value }}
            </option>
        @endforeach
    </select>
</div>