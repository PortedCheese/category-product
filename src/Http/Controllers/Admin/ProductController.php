<?php

namespace PortedCheese\CategoryProduct\Http\Controllers\Admin;

use App\AddonType;
use App\Category;
use App\Http\Controllers\Controller;
use App\Meta;
use App\Product;
use App\ProductCollection;
use App\ProductLabel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PortedCheese\CategoryProduct\Events\CategorySpecificationValuesUpdate;
use PortedCheese\CategoryProduct\Events\ProductListChange;
use PortedCheese\CategoryProduct\Facades\CategoryActions;
use PortedCheese\CategoryProduct\Facades\ProductActions;

class ProductController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->authorizeResource(Product::class, "product");
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Category $category
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, Category $category = null)
    {
        $types = AddonType::all();
        $collection = Product::query()
            ->with("category");
        if (! empty($category)) {
            $collection->where("category_id", $category->id);
            $fromRoute = route("admin.categories.products.index", ["category" => $category]);
        }
        else {
            $fromRoute = route("admin.products.index");
        }
        if ($title = $request->get("title", false)) {
            $collection->where("title", "like", "%$title%");
        }
        if ($published = $request->get("published", "all")) {
            if ($published == "no") {
                $collection->whereNull("published_at");
            }
            elseif ($published == "yes") {
                $collection->whereNotNull("published_at");
            }
        }
        if ($addonType = $request->get("addon_type", false)) {
            if ($addonType === "product")
                $collection->whereNull("addon_type_id");
            else
                $collection->where("addon_type_id", "=", "$addonType");
        }

        $collection->orderBy("published_at", "desc");
        $products = $collection->paginate()->appends($request->input());
        return view(
            "category-product::admin.products.index",
            compact("category", "types", "fromRoute", "products", "request")
        );
    }

    /**
     * Show create product view
     *
     * @param Request $request
     * @param Category $category
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function create(Request $request, Category $category)
    {
        $types = null;
        if ($addon = $request->get("addon", false)) {
            $types = AddonType::all();
        }
        $labels = ProductLabel::query()->orderBy("title")->get();
        $collections = ProductCollection::query()->orderBy("title")->get();
        return view(
            "category-product::admin.products.create".($addon? "-addon":""),
            compact("category", "labels", "collections", "types", "request")
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Category $category)
    {
        $this->storeValidator($request->all());
        $product = $category->products()->create($request->all());
        if ($addonTypeId = $request->get("addon_type", false)) {
            $type = AddonType::find($addonTypeId);
            if ($type)
            {
                $type->addons()->save($product);
                return redirect()
                    ->route("admin.products.show", ["product" => $product])
                    ->with("success", "Товар-дополнение добавлен");
            }
        }
        /**
         * @var Product $product
         */
        // Метки.
        $labels = $request->get("labels", []);
        $product->labels()->sync($labels);
        // Коллекции.
        $collections = $request->get("collections", []);
        $product->collections()->sync($collections);
        return redirect()
            ->route("admin.products.show", ["product" => $product])
            ->with("success", "Товар добавлен");
    }

    /**]
     * @param $data
     */
    protected function storeValidator($data)
    {
        Validator::make($data, [
            "title" => ["required", "max:100", "unique:products,title"],
            "slug" => ["nullable", "max:250", "unique:products,slug"],
            "short" => ["nullable", "max:150"],
            "description" => ["required"],
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
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $category = $product->category;
        $labels = $product->labels;
        $collections = $product->collections;
        $categories = CategoryActions::getAllList();
        return view(
            "category-product::admin.products.show",
            compact("product", "category", "labels", "collections", "categories")
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function edit(Product $product)
    {
        $category = $product->category;
        $types = null;
        if ($addon = $product->addonType) {
            $types = AddonType::all();
        }
        $labels = ProductLabel::query()->orderBy("title")->get();
        $currentLabels = [];
        foreach ($product->labels as $label) {
            $currentLabels[] = $label->id;
        }
        $collections = ProductCollection::query()->orderBy("title")->get();
        $currentCollections = [];
        foreach ($product->collections as $collection) {
            $currentCollections[] = $collection->id;
        }
        return view(
            "category-product::admin.products.edit".($addon? "-addon":""),
            compact("product", "category", "labels", "currentLabels", "collections","currentCollections", "types")
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Product $product)
    {
        $this->updateValidator($request->all(), $product);
        // Обновление.
        $product->update($request->all());
        // Тип дополнения
        if ($addonTypeId = $request->get("addon_type", false)) {
            $type = AddonType::find($addonTypeId);
            if ($type && $product->addonType->id !== $type->id)
            {
                $product->addon_type_id = $type->id;
                $product->save();
            }
        }
        // Метки.
        $labels = $request->get("labels", []);
        $product->labels()->sync($labels);
        // Коллекции.
        $collections = $request->get("collections", []);
        $product->collections()->sync($collections);
        $product->clearCache();

        return redirect()
            ->route("admin.products.show", ["product" => $product])
            ->with("success", "Товар обновлен");
    }

    /**
     * @param $data
     * @param Product $product
     */
    protected function updateValidator($data, Product $product)
    {
        $id = $product->id;
        Validator::make($data, [
            "title" => ["required", "max:100", "unique:products,title,{$id}"],
            "slug" => ["nullable", "max:250", "unique:products,slug,{$id}"],
            "short" => ["nullable", "max:150"],
            "description" => ["required"],
        ], [], [
            "title" => "Заголовок",
            "slug" => "Адресная строка",
            "short" => "Краткое описание",
            "description" => "Описание",
        ])->validate();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Product $product)
    {
        $category = $product->category;
        $product->delete();

        return redirect()
            ->route("admin.categories.products.index", ["category" => $category])
            ->with("success", "Товар удален");
    }

    /**
     * Meta.
     *
     * @param Product $product
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function meta(Product $product)
    {
        $this->authorize("viewAny", Meta::class);
        $this->authorize("update", $product);
        $category = $product->category;
        return view("category-product::admin.products.meta", compact("category", "product"));
    }

    /**
     * Галерея товара.
     *
     * @param Product $product
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function gallery(Product $product)
    {
        $this->authorize("update", $product);
        $category = $product->category;
        return view("category-product::admin.products.gallery", compact("category", "product"));
    }

    /**
     * Изменить категорию товара.
     *
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function changeCategory(Request $request, Product $product)
    {
        $this->authorize("changeCategory", $product);
        $this->changeCategoryValidator($request->all());
        ProductActions::changeCategory($product, $request->get("category_id"));
        return redirect()
            ->route("admin.products.show", ["product" => $product])
            ->with("success", "Категория изменена");
    }

    /**
     * @param $data
     */
    protected function changeCategoryValidator($data)
    {
        Validator::make($data, [
            "category_id" => "required|exists:categories,id",
        ], [], [
            "category_id" => "Категория",
        ])->validate();
    }

    /**
     * Спена статуса публикации.
     *
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function changePublished(Product $product)
    {
        $this->authorize("publish", $product);
        if ($product->category->published_at || $product->published_at) {
            $product->published_at = $product->published_at ? null : now();
        }
        else
        {
            return redirect()
                ->back()
                ->with("danger", "Статус публикации не может быть изменен: категория не опубликована");
        }
        $product->save();
        $category = $product->category;
        // При отключении товара меняется набор характеристик для фильтрации.
        event(new CategorySpecificationValuesUpdate($category));
        // Вызвать событие изменения списка товаров.
        event(new ProductListChange($category));
        //event(new ProductCollectionListChange($collection));
        return redirect()
            ->back()
            ->with("success", "Статус публикации изменен");
    }
}
