@extends("admin.layout")

@section("page-title", "Приоритет груп - ")

@section('header-title', "Приоритет груп")

@section('admin')
    @include("category-product::admin.specification-groups.includes.pills")
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <universal-priority
                        :elements="{{ json_encode($groups) }}"
                        url="{{ route("admin.vue.priority", ['table' => "specification_groups", "field" => "priority"]) }}">
                </universal-priority>
            </div>
        </div>
    </div>
@endsection