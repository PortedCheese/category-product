<?php

namespace PortedCheese\CategoryProduct\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Meta;
use App\ProductCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductCollectionController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->authorizeResource(ProductCollection::class, "collection");
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
            $items = ProductCollection::query()
                ->select("title", "id", "slug")
                ->orderBy("priority")
                ->get();
            $collections = [];
            foreach ($items as $item) {
                $collections[] = [
                    'name' => $item->title,
                    "id" => $item->id,
                    "url" => route("admin.product-collections.show", ["collection" => $item])
                ];
            }
        }
        else{
            $collection = ProductCollection::query();
            if ($title = $request->get("title", false)) {
                $collection->where("title", "like", "%$title%");
            }
            $collection->orderBy("priority");
            $collections = $collection->paginate()->appends($request->input());
        }

        return view(
            "category-product::admin.product-collections.index",
            compact("request", "collections", "isTree")
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("category-product::admin.product-collections.create");
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
        $item = ProductCollection::create($request->all());
        $item->uploadImage($request, "product_collections", "image");
        return redirect()
            ->route("admin.product-collections.index")
            ->with("success", "Коллекция добавлена");
    }

    protected function storeValidator($data)
    {
        Validator::make($data, [
            "title" => ["required", "max:30", "unique:product_collections,title"],
            "slug" => ["nullable", "max:30", "unique:product_collections,slug"],
            "short" => ["nullable", "max:255"],
            "description" => ["nullable"],
            "image" => ["nullable", "image"],
        ], [], [
            "title" => "Заголовок",
            "slug" => "Адресная строка",
            "short" => "Краткое описание",
            "description" => "Описание",
            "image" => "Изображение",
        ])->validate();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ProductCollection  $collection
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, ProductCollection $collection)
    {
        $items = $collection->products()
            ->with("category");
        if ($title = $request->get("title", false)) {
            $items->where("title", "like", "%$title%");
        }
        $items->orderBy("title");
        $products = $items->paginate()->appends($request->input());
        return view(
            "category-product::admin.product-collections.show",
            compact("collection", "products", "request")
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ProductLabel  $collection
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductCollection $collection)
    {
        return view("category-product::admin.product-collections.edit", compact("collection"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ProductCollection  $collection
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductCollection $collection)
    {
        $this->updateValidator($request->all(), $collection);
        $collection->update($request->all());
        $collection->uploadImage($request, "product_collections", "image");

        return redirect()
            ->route("admin.product-collections.show", ["collection" => $collection])
            ->with("success", "Коллекция успешно обновлена");
    }

    protected function updateValidator($data, ProductCollection $collection)
    {
        $id = $collection->id;
        Validator::make($data, [
            "title" => ["required", "max:30", "unique:product_collections,title,{$id}"],
            "slug" => ["nullable", "max:30", "unique:product_collections,slug,{$id}"],
            "short" => ["nullable", "max:255"],
            "description" => ["nullable"],
            "image" => ["nullable", "image"],
        ], [], [
            "title" => "Заголовок",
            "slug" => "Адресная строрка",
            "short" => "Краткое описание",
            "description" => "Описание",
            "image" => "Изображение",
        ])->validate();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ProductCollection $collection
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(ProductCollection $collection)
    {
        $collection->delete();

        return redirect()
            ->route("admin.product-collections.index")
            ->with("success", "Коллекция успешно удалена");
    }

    /**
     * Удалить главное изображение.
     *
     * @param ProductCollection $collection
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function deleteImage(ProductCollection $collection)
    {
        $this->authorize("update", $collection);
        $collection->clearImage();
        return redirect()
            ->back()
            ->with('success', 'Изображение удалено');
    }

    /**
     * Смена статуса публикации.
     *
     * @param ProductCollection $collection
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function changePublished(ProductCollection $collection)
    {
        $this->authorize("publish", $collection);
        $collection->published_at = $collection->published_at ? null : now();
        $collection->save();

        return redirect()
            ->back()
            ->with("success", "Статус публикации изменен");
    }

    /**
     * Метатеги.
     *
     * @param ProductCollection $collection
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function metas(ProductCollection $collection)
    {
        $this->authorize("viewAll", Meta::class);
        $this->authorize("update", $collection);

        return view("category-product::admin.product-collections.metas", [
            'collection' => $collection,
        ]);
    }
}
