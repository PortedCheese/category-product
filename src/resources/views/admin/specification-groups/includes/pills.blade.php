<div class="col-12 mb-2">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-pills">
                @can('viewAny', \App\SpecificationGroup::class)
                    <li class="nav-item">
                        <a href="{{ route("admin.specification-groups.index") }}"
                           class="nav-link{{ $currentRoute === "admin.specification-groups.index" ? " active" : "" }}">
                            Список
                        </a>
                    </li>
                @endcan

                @can("update")
                    <li class="nav-item">
                        <a href="{{ route("admin.specification-groups.priority") }}"
                           class="nav-link{{ $currentRoute === "admin.specification-groups.priority" ? " active" : "" }}">
                            Приоритет
                        </a>
                    </li>
                @endcan

                @can("create", \App\SpecificationGroup::class)
                    <li class="nav-item">
                        <a href="{{ route("admin.specification-groups.create") }}"
                           class="nav-link{{ $currentRoute === "admin.specification-groups.create" ? " active" : "" }}">
                            Добавить
                        </a>
                    </li>
                @endcan

                @if (! empty($group))
                    @can("view", $group)
                        <li class="nav-item">
                            <a href="{{ route("admin.specification-groups.show", ["group" => $group]) }}"
                               class="nav-link{{ $currentRoute === "admin.specification-groups.show" ? " active" : "" }}">
                                Просмотр
                            </a>
                        </li>
                    @endcan

                    @can("delete", $group)
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
                    @endcan
                @endif
            </ul>
        </div>
    </div>
</div>