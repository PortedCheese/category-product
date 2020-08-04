<div class="col-12 mb-2">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a href="{{ route("admin.product-labels.index") }}"
                       class="nav-link{{ $currentRoute === "admin.product-labels.index" ? " active" : "" }}">
                        Список
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route("admin.product-labels.create") }}"
                       class="nav-link{{ $currentRoute === "admin.product-labels.create" ? " active" : "" }}">
                        Добавить
                    </a>
                </li>

                @if (! empty($label))
                    <li class="nav-item">
                        <a href="{{ route("admin.product-labels.show", ["label" => $label]) }}"
                           class="nav-link{{ $currentRoute === "admin.product-labels.show" ? " active" : "" }}">
                            Просмотр
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route("admin.product-labels.edit", ["label" => $label]) }}"
                           class="nav-link{{ $currentRoute === "admin.product-labels.edit" ? " active" : "" }}">
                            Редактировать
                        </a>
                    </li>

                    <li class="nav-item">
                        <button type="button" class="btn btn-link nav-link"
                                data-confirm="{{ "delete-form-label-{$label->id}" }}">
                            <i class="fas fa-trash-alt text-danger"></i>
                        </button>
                        <confirm-form :id="'{{ "delete-form-label-{$label->id}" }}'">
                            <template>
                                <form action="{{ route('admin.product-labels.destroy', ['label' => $label]) }}"
                                      id="delete-form-label-{{ $label->id }}"
                                      class="btn-group"
                                      method="post">
                                    @csrf
                                    @method("delete")
                                </form>
                            </template>
                        </confirm-form>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>