@if ($filter->render)
    <div class="form-group steps-slider-cover" data-step="1">
        <label>{{ $filter->title }}</label>
        <div class="row justify-content-between my-2">
            <div class="col-6">
                <input type="number"
                       name="range-from-{{ $filter->slug }}"
                       aria-label="От"
                       step="1"
                       min="{{ (int) min($filter->values) }}"
                       max="{{ (int) max($filter->values) }}"
                       data-value="{{ (int) min($filter->values) }}"
                       data-init="{{ $request->get("range-from-{$filter->slug}", (int) min($filter->values)) }}"
                       class="form-control from-input">
            </div>
            <div class="col-6">
                <input type="number"
                       name="range-to-{{ $filter->slug }}"
                       aria-label="До"
                       step="1"
                       min="{{ (int) min($filter->values) }}"
                       max="{{ (int) max($filter->values) }}"
                       data-value="{{(int) max($filter->values) }}"
                       data-init="{{ $request->get("range-to-{$filter->slug}", (int) max($filter->values)) }}"
                       class="form-control to-input">
            </div>
        </div>
        <div class="row">
            <div class="col-12 mt-2">
                <div class="steps-slider"></div>
            </div>
        </div>
    </div>
@endif