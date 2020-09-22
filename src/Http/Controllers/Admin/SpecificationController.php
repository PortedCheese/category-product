<?php

namespace PortedCheese\CategoryProduct\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Specification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PortedCheese\CategoryProduct\Events\CategorySpecificationUpdate;
use PortedCheese\CategoryProduct\Facades\CategoryActions;
use PortedCheese\CategoryProduct\Facades\SpecificationActions;

class SpecificationController extends Controller
{
    public function list(Request $request)
    {
        $this->authorize("viewAny", Specification::class);
        
        $collection = Specification::query()
            ->with("group");
        if ($title = $request->get("title", false)) {
            $collection->where("title", "like", "%$title%");
        }
        $specifications = $collection
            ->orderBy("title")
            ->paginate()
            ->appends($request->input());
        return view(
            "category-product::admin.specifications.list",
            compact("specifications", "request")
        );
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Category $category)
    {
        $specifications = $category->specifications()
            ->with("group")
            ->orderBy("priority")
            ->get();
        return view(
            "category-product::admin.specifications.index",
            compact("category", "specifications")
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Category $category)
    {
        $types = Specification::getTypes();
        $nextSpec = $category->specifications()->count() + 1;
        $groups = SpecificationActions::getGroups();
        $available = SpecificationActions::getAvailableForCategory($category);
        return view(
            "category-product::admin.specifications.create",
            compact("category", "types", "nextSpec", "groups", "available")
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Category $category)
    {
        $this->storeValidator($request->all());
        $exist = $request->get("exists", false);
        if ($exist) {
            $specification = Specification::find($exist);
        }
        else {
            $specification = Specification::create($request->all());
        }
        
        $title = $request->get("title", false);
        if (empty($title)) {
            $title = $specification->title;
        }
        /**
         * @var Specification $specification
         */
        $specification->categories()->attach($category, [
            "title" => $title,
            "filter" => $request->has("filter") ? 1 : 0,
            "priority" => $request->get("priority", 1),
        ]);

        event(new CategorySpecificationUpdate($category));

        return redirect()
            ->route("admin.categories.specifications.index", ["category" => $category])
            ->with("success", "Характеристика добавлена");
    }

    /**
     * @param array $data
     */
    protected function storeValidator(array $data)
    {
        Validator::make($data, [
            "title" => ["nullable", "required_without:exists", "max:150"],
            "exists" => ["nullable", "required_without_all:machine,type,title", "exists:specifications,id"],
            "type" => ["nullable", "required_without:exists"],
            "slug" => ["nullable", "max:100", "unique:specifications,slug"],
        ], [], [
            "title" => "Заголовок",
            "exists" => "Существующие",
            "type" => "Тип",
            "slug" => "Ключ",
        ])->validate();
    }

    /**
     * Просмотр.
     *
     * @param Specification $specification
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Specification $specification)
    {
        $categories = $specification->categories;
        $groups = SpecificationActions::getGroups();
        $types = Specification::getTypes();
        $group = $specification->group;
        return view(
            "category-product::admin.specifications.show",
            compact("specification", "categories", "types", "groups", "group")
        );
    }

    /**
     * Обновление.
     *
     * @param Request $request
     * @param Specification $specification
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Specification $specification)
    {
        $this->updateValidator($request->all());
        $specification->update($request->all());
        $categories = $specification->categories;
        if ($categories->count()) {
            foreach ($categories as $category) {
                event(new CategorySpecificationUpdate($category));
            }
        }
        return redirect()
            ->back()
            ->with("success", "Обновлено");
    }

    /**
     * @param $data
     */
    protected function updateValidator($data)
    {
        Validator::make($data, [
            "title" => ["required", "max:150"],
            "type" => ["required"],
        ], [], [
            "title" => "Заголвок",
            "type" => "Виджет поля",
        ])->validate();
    }

    /**
     * Синхронизировать название.
     *
     * @param Specification $specification
     * @return \Illuminate\Http\RedirectResponse
     */
    public function syncTitle(Specification $specification)
    {
        foreach ($specification->categories as $category) {
            /**
             * @var Category $category
             */
            $category->specifications()
                ->updateExistingPivot($specification->id, [
                    "title" => $specification->title,
                ]);
            event(new CategorySpecificationUpdate($category));
        }
        return redirect()
            ->back()
            ->with("success", "Заголовоки успешно обновлены");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Category $category
     * @param  \App\Specification  $specification
     * @return \Illuminate\Http\Response
     */
    public function editPivot(Category $category, Specification $specification)
    {
        $pivot = $specification->categories()->find($category->id)->pivot;
        return view(
            "category-product::admin.specifications.edit",
            compact("category", "specification", "pivot")
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Specification  $specification
     * @return \Illuminate\Http\Response
     */
    public function updatePivot(Request $request, Category $category, Specification $specification)
    {
        $this->updatePivotValidator($request->all());

        $category->specifications()
            ->updateExistingPivot($specification->id, [
                "title" => $request->get("title"),
                "filter" => $request->has("filter") ? 1 : 0,
                "priority" => $request->get("priority", 1)
            ]);
        event(new CategorySpecificationUpdate($category));
        return redirect()
            ->route("admin.categories.specifications.index", ["category" => $category])
            ->with("success", "Успешно обновлено");
    }

    protected function updatePivotValidator($data)
    {
        Validator::make($data, [
            "title" => ["required", "max:150"],
            "priority" => ["required", "numeric", "min:1"],
        ], [], [
            "title" => "Заголовок",
            "priority" => "Приоритет",
        ])->validate();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @param Specification $specification
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroyPivot(Category $category, Specification $specification)
    {
        if (SpecificationActions::checkProductsSpecifications($category, $specification)) {
            return redirect()
                ->back()
                ->with("danger", "У характеристики есть заполненные значения");
        }

        $category->specifications()->detach($specification);
        $specification->checkCategoryOnDetach();
        event(new CategorySpecificationUpdate($category));
        return redirect()
            ->route("admin.categories.specifications.index", ["category" => $category])
            ->with("success", "Характеристика удалена");
    }

    /**
     * Синхронизировать характеристики.
     * 
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sync(Category $category)
    {
        CategoryActions::syncSpec($category);
        return redirect()
            ->back()
            ->with("success", "Характеристики синхронизированны");
    }
}
