@extends("admin.layout")

@section("page-title", "Просмотр {$category->title} - ")

@section('header-title', "Просмотр {$category->title}")

@section('admin')
    @include("category-product::admin.categories.includes.pills")
    @if ($image)
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    @img([
                        "image" => $image,
                        "template" => "medium",
                        "lightbox" => "lightGroup" . $category->id,
                        "imgClass" => "rounded mb-2",
                        "grid" => [],
                    ])
                </div>
            </div>
        </div>
    @endif
    <div class="{{ $image ? "col-12 col-lg-8" : "col-12" }}">
        <div class="card">
            <div class="card-body">
                <dl class="row">
                    @if ($category->short)
                        <dt class="col-sm-3">Описание</dt>
                        <dd class="col-sm-9">{{ $category->short }}</dd>
                    @endif
                    @if ($category->parent)
                        <dt class="col-sm-3">Родитель</dt>
                        <dd class="col-sm-9">
                            <a href="{{ route("admin.categories.show", ["category" => $category->parent]) }}">
                                {{ $category->parent->title }}
                            </a>
                        </dd>
                    @endif
                    @if ($childrenCount)
                        <dt class="col-sm-3">Дочернии</dt>
                        <dd class="col-sm-9">{{ $childrenCount }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
    @if ($childrenCount)
        <div class="col-12 mt-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Подкатегории</h5>
                </div>
                <div class="card-body">
                    @include("category-product::admin.categories.includes.table-list", ["categories" => $children])
                </div>
            </div>
        </div>
    @endif
@endsection