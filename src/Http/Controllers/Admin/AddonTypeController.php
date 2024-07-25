<?php

namespace PortedCheese\CategoryProduct\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Meta;
use App\ProductCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\AddonType;

class AddonTypeController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->authorizeResource(AddonType::class, "addon_type");
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $view = $request->get("view","default");
        $isTree = $view == "tree";
        if ($isTree) {
            $items = AddonType::query()
                ->select("title", "id", "slug")
                ->orderBy("priority")
                ->get();
            $types = [];
            foreach ($items as $item) {
                $types[] = [
                    'name' => $item->title,
                    "id" => $item->id,
                    "url" => route("admin.addon-types.show", ["type" => $item])
                ];
            }
        }
        else{
            $collection = AddonType::query();
            if ($title = $request->get("title", false)) {
                $collection->where("title", "like", "%$title%");
            }
            $collection->orderBy("priority");
            $types = $collection->paginate()->appends($request->input());
        }

        return view(
            "category-product::admin.addon-types.index",
            compact("request", "types", "isTree")
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("category-product::admin.addon-types.create");
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
        $item = AddonType::create($request->all());
        return redirect()
            ->route("admin.addon-types.index")
            ->with("success", "Добавлено");
    }

    protected function storeValidator($data)
    {
        Validator::make($data, [
            "title" => ["required", "max:30", "unique:addon_types,title"],
            "slug" => ["nullable", "max:30", "unique:addon_types,slug"],
            "short" => ["nullable", "max:255"],
            "description" => ["nullable"],
        ], [], [
            "title" => "Заголовок",
            "slug" => "Адресная строка",
            "short" => "Краткое описание",
            "description" => "Описание",
        ])->validate();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AddonType  $type
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, AddonType $type)
    {
        $items = $type->addons()
            ->with("category");
        if ($title = $request->get("title", false)) {
            $items->where("title", "like", "%$title%");
        }
        $items->orderBy("title");
        $addons = $items->paginate()->appends($request->input());
        return view(
            "category-product::admin.addon-types.show",
            compact("type", "addons", "request")
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ProductLabel  $type
     * @return \Illuminate\Http\Response
     */
    public function edit(AddonType $type)
    {
        return view("category-product::admin.addon-types.edit", compact("type"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AddonType  $type
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AddonType $type)
    {
        $this->updateValidator($request->all(), $type);
        $type->update($request->all());

        return redirect()
            ->route("admin.addon-types.show", ["type" => $type])
            ->with("success", "Успешно обновлено");
    }

    protected function updateValidator($data, AddonType $type)
    {
        $id = $type->id;
        Validator::make($data, [
            "title" => ["required", "max:30", "unique:addon_types,title,{$id}"],
            "slug" => ["nullable", "max:30", "unique:addon_types,slug,{$id}"],
            "short" => ["nullable", "max:255"],
            "description" => ["nullable"],
        ], [], [
            "title" => "Заголовок",
            "slug" => "Адресная строрка",
            "short" => "Краткое описание",
            "description" => "Описание",
        ])->validate();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AddonType $type
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(AddonType $type)
    {
        $type->delete();

        return redirect()
            ->route("admin.addon-types.index")
            ->with("success", "Успешно удалено");
    }
}
