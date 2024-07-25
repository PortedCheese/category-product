<div class="col-12 mb-2">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-pills">
                @can("viewAll", \App\AddonType::class)
                    <li class="nav-item">
                        <a href="{{ route("admin.addon-types.index") }}"
                           class="nav-link{{ $currentRoute === "admin.addon-types.index" &&  !$isTree ? " active" : "" }}">
                            Список
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.addon-types.index') }}?view=tree"
                           class="nav-link{{ isset($isTree) && $isTree ? " active" : "" }}">
                            Приоритет
                        </a>
                    </li>
                @endcan

                @can("create", \App\AddonType::class)
                    <li class="nav-item">
                        <a href="{{ route("admin.addon-types.create") }}"
                           class="nav-link{{ $currentRoute === "admin.addon-types.create" ? " active" : "" }}">
                            Добавить
                        </a>
                    </li>
                @endcan

                @if (! empty($type))
                    @can("view", \App\AddonType::class)
                        <li class="nav-item">
                            <a href="{{ route("admin.addon-types.show", ["type" => $type]) }}"
                               class="nav-link{{ $currentRoute === "admin.addon-types.show" ? " active" : "" }}">
                                Просмотр
                            </a>
                        </li>
                    @endcan
                    @can("update", \App\AddonType::class)
                        <li class="nav-item">
                            <a href="{{ route("admin.addon-types.edit", ["type" => $type]) }}"
                               class="nav-link{{ $currentRoute === "admin.addon-types.edit" ? " active" : "" }}">
                                Редактировать
                            </a>
                        </li>
                    @endcan

                    @can("delete", \App\AddonType::class)
                        <li class="nav-item">
                            <button type="button" class="btn btn-link nav-link"
                                    data-confirm="{{ "delete-form-type-{$type->id}" }}">
                                <i class="fas fa-trash-alt text-danger"></i>
                            </button>
                            <confirm-form :id="'{{ "delete-form-type-{$type->id}" }}'">
                                <template>
                                    <form action="{{ route('admin.addon-types.destroy', ['type' => $type]) }}"
                                          id="delete-form-type-{{ $type->id }}"
                                          class="btn-group"
                                          method="post">
                                        @csrf
                                        @method("delete")
                                    </form>
                                </template>
                            </confirm-form>
                        </li>
                    @endcan
                @endif
            </ul>
        </div>
    </div>
</div>