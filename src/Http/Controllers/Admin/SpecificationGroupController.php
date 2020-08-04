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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SpecificationGroup  $specification
     * @return \Illuminate\Http\Response
     */
    public function edit(SpecificationGroup $group)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SpecificationGroup  $specification
     * @return \Illuminate\Http\Response
     */
    public function destroy(SpecificationGroup $group)
    {
        //
    }
}
