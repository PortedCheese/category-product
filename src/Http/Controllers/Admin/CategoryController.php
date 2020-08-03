<?php

namespace PortedCheese\CategoryProduct\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Meta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->authorizeResource(Category::class, "category");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $view = $request->get("view", "default");
        $isTree = $view == "tree";
        if ($isTree) {
            $categories = [];
        }
        else {
            $collection = Category::query()
                ->whereNull("parent_id")
                ->orderBy("priority", "asc");
            $categories = $collection->get();
        }
        return view("category-product::admin.categories.index", compact("categories", "isTree"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Category $category = null)
    {
        return view("category-product::admin.categories.create", [
            "category" => $category,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Category $category = null)
    {
        $this->storeValidator($request->all());
        if (empty($category)) {
            $item = Category::create($request->all());
        }
        else {
            $item = $category->children()->create($request->all());
        }
        /**
         * @var Category $category
         */
        $item->uploadImage($request, "categories", "image");
        return redirect()
            ->route("admin.categories.show", ["category" => $item])
            ->with("success", "Категория добавлена");
    }

    /**
     * @param array $data
     */
    protected function storeValidator(array $data)
    {
        Validator::make($data, [
            "title" => ["required", "max:100"],
            "slug" => ["nullable", "max:100", "unique:categories,slug"],
            "image" => ["nullable", "image"],
            "short" => ["nullable", "max:250"],
        ], [], [
            "title" => "Заголовок",
            "slug" => "Адресная строка",
            "image" => "Изображение",
            "short" => "Краткое описание",
        ])->validate();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        $image = $category->image;
        $childrenCount = $category->children->count();
        if ($childrenCount) {
            $children = $category->children()->orderBy("priority")->get();
        }
        else {
            $children = false;
        }
        return view("category-product::admin.categories.show", compact("category", "image", "childrenCount", "children"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        return view("category-product::admin.categories.edit", compact("category"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Category $category)
    {
        $this->updateValidator($request->all(), $category);
        $category->update($request->all());
        $category->uploadImage($request, "categories", "image");
        return redirect()
            ->route("admin.categories.show", ["category" => $category])
            ->with("success", "Категория успешно обновлена");
    }

    /**
     * @param array $data
     * @param Category $category
     */
    protected function updateValidator(array $data, Category $category)
    {
        $id = $category->id;
        Validator::make($data, [
            "title" => ["required", "max:100"],
            "slug" => ["nullable", "max:100", "unique:categories,slug,{$id}"],
            "image" => ["nullable", "image"],
            "short" => ["nullable", "max:250"],
        ], [], [
            "title" => "Заголовок",
            "slug" => "Адресная строка",
            "image" => "Изображение",
            "short" => "Краткое описание",
        ])->validate();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        if ($category->children->count()) {
            return redirect()
                ->back()
                ->with("danger", "Невозможно удалить категорию, у нее есть подкатегории");
        }
        // TODO: check products
        $parent = $category->parent;
        $category->delete();
        if ($parent) {
            return redirect()
                ->route("admin.categories.show", ["category" => $parent])
                ->with("success", "Категория успешно удалена");
        }
        else {
            return redirect()
                ->route("admin.categories.index")
                ->with("success", "Категория успешно удалена");
        }
    }

    /**
     * Метатеги.
     *
     * @param Category $category
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function metas(Category $category)
    {
        $this->authorize("viewAny", Meta::class);
        $this->authorize("update", $category);

        return view("category-product::admin.categories.metas", [
            'category' => $category,
        ]);
    }
}