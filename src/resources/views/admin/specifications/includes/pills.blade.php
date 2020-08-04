@include("category-product::admin.categories.includes.pills")
<div class="col-12 mb-2">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a href="{{ route("admin.categories.specifications.index", ["category" => $category]) }}"
                       class="nav-link{{ $currentRoute === "admin.categories.specifications.index" ? " active" : "" }}">
                        Список
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route("admin.categories.specifications.create", ["category" => $category]) }}"
                       class="nav-link{{ $currentRoute === "admin.categories.specifications.create" ? " active" : "" }}">
                        Добавить
                    </a>
                </li>

                @if (! empty($specification))
                    <li class="nav-item">
                        <a href="{{ route("admin.categories.specifications.edit", ["category" => $category, "specification" => $specification]) }}"
                           class="nav-link{{ $currentRoute === "admin.categories.specifications.edit" ? " active" : "" }}">
                            Редактировать
                        </a>
                    </li>

                    <li class="nav-item">
                        <button type="button" class="btn btn-link nav-link"
                                data-confirm="{{ "delete-form-specification-{$specification->id}" }}">
                            <i class="fas fa-trash-alt text-danger"></i>
                        </button>
                        <confirm-form :id="'{{ "delete-form-specification-{$specification->id}" }}'">
                            <template>
                                <form action="{{ route('admin.categories.specifications.destroy', ["category" => $category, 'specification' => $specification]) }}"
                                      id="delete-form-specification-{{ $specification->id }}"
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