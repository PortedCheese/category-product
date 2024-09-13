<div class="product-filters-modal">
    <button class="btn product-filters-modal__btn" data-toggle="modal" data-target="#filterModal">Фильтры</button>
</div>

<div class="modal slide dir-left" tabindex="-1" aria-hidden="true" role="dialog" id="filterModal">
    <div class="modal-dialog modal-dialog-centered ms-0 my-0" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Фильтры</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include("category-product::site.filters.form", ["modal" => true])
            </div>
        </div>
    </div>
</div>