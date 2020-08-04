<div class="col-12 mb-2">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a href="{{ route("admin.specification-groups.index") }}"
                       class="nav-link{{ $currentRoute === "admin.specification-groups.index" ? " active" : "" }}">
                        Список
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route("admin.specification-groups.create") }}"
                       class="nav-link{{ $currentRoute === "admin.specification-groups.create" ? " active" : "" }}">
                        Добавить
                    </a>
                </li>

                @if (! empty($group))
                    <li class="nav-item">
                        <a href="{{ route("admin.specification-groups.show", ["group" => $group]) }}"
                           class="nav-link{{ $currentRoute === "admin.specification-groups.show" ? " active" : "" }}">
                            Просмотр
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route("admin.specification-groups.edit", ["group" => $group]) }}"
                           class="nav-link{{ $currentRoute === "admin.specification-groups.edit" ? " active" : "" }}">
                            Редактировать
                        </a>
                    </li>

                    @can("viewAny", \App\Meta::class)
                        <li class="nav-item">
                            <a href="{{ route("admin.specification-groups.meta", ["group" => $group]) }}"
                               class="nav-link{{ $currentRoute === "admin.specification-groups.meta" ? " active" : "" }}">
                                Метатеги
                            </a>
                        </li>
                    @endcan

                    <li class="nav-item">
                        <button type="button" class="btn btn-link nav-link"
                                data-confirm="{{ "delete-form-group-{$group->id}" }}">
                            <i class="fas fa-trash-alt text-danger"></i>
                        </button>
                        <confirm-form :id="'{{ "delete-form-group-{$group->id}" }}'">
                            <template>
                                <form action="{{ route('admin.specification-groups.destroy', ['group' => $group]) }}"
                                      id="delete-form-group-{{ $group->id }}"
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