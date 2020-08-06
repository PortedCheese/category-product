<?php

namespace PortedCheese\CategoryProduct\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\SpecificationGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SpecificationGroupController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->authorizeResource(SpecificationGroup::class, "group");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $collection = SpecificationGroup::query();
        if ($title = $request->get("title", false)) {
            $collection->where("title", "like", "%$title%");
        }
        $collection->orderBy("priority");
        $groups = $collection->paginate()->appends($request->input());
        return view("category-product::admin.specification-groups.index", compact("groups", "request"));
    }

    /**
     * Приоритет.
     * 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function priority()
    {
        $this->authorize("update", SpecificationGroup::class);
        $collection = SpecificationGroup::query()
            ->orderBy("priority")
            ->get();
        $groups = [];
        foreach ($collection as $item) {
            $groups[] = [
                "name" => $item->title,
                "id" => $item->id,
            ];
        }
        return view("category-product::admin.specification-groups.priority", compact("groups"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("category-product::admin.specification-groups.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->storeValidator($request->all());
        $group = SpecificationGroup::create($request->all());
        return redirect()
            ->route("admin.specification-groups.index")
            ->with("success", "Характеристика добавлена");
    }

    /**
     * @param $data
     */
    protected function storeValidator($data)
    {
        Validator::make($data, [
            "title" => ["required", "max:100", "unique:specification_groups,title"],
            "slug" => ["nullable", "max:100", "unique:specification_groups,slug"],
        ], [], [
            "title" => "Заголовок",
            "slug" => "Ключ",
        ])->validate();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SpecificationGroup  $group
     * @return \Illuminate\Http\Response
     */
    public function show(SpecificationGroup $group)
    {
        $specifications = $group->specifications()->orderBy("title")->get();
        return view("category-product::admin.specification-groups.show", compact("group", "specifications"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SpecificationGroup  $specification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SpecificationGroup $group)
    {
        $this->updateValidator($request->all(), $group);
        $group->update($request->all());
        return redirect()
            ->back()
            ->with("success", "Обновлено");
    }

    protected function updateValidator($data, SpecificationGroup $group)
    {
        $id = $group->id;
        Validator::make($data, [
            "title" => ["required", "max: 100", "unique:specification_groups,title,{$id}"]
        ], [], [
            "title" => "Заголовок",
        ])->validate();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param SpecificationGroup $group
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(SpecificationGroup $group)
    {
        $group->delete();
        return redirect()
            ->route("admin.specification-groups.index")
            ->with("success", "Группа удалена");
    }
}
