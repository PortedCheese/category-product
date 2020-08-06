<?php

namespace PortedCheese\CategoryProduct\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Product;
use App\ProductLabel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $collection->orderBy("published_at", "desc");
        $products = $collection->paginate()->appends($request->input());
        return view(
            "category-product::admin.products.index",
            compact("category", "fromRoute", "products", "request")
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Category $category
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Category $category)
    {
        $labels = ProductLabel::query()->orderBy("title")->get();
        return view(
            "category-product::admin.products.create",
            compact("category", "labels")
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
        /**
         * @var Product $product
         */
        // Метки.
        $labels = $request->get("labels", []);
        $product->labels()->sync($labels);
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
            "title" => ["required", "max:250", "unique:products,title"],
            "slug" => ["nullable", "max:250", "unique:products,slug"],
            "short" => ["nullable", "max:250"],
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
        return view(
            "category-product::admin.products.show",
            compact("product", "category", "labels")
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $category = $product->category;
        $labels = ProductLabel::query()->orderBy("title")->get();
        $currentLabels = [];
        foreach ($product->labels as $label) {
            $currentLabels[] = $label->id;
        }
        return view(
            "category-product::admin.products.edit",
            compact("product", "category", "labels", "currentLabels")
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
        $product->update($request->all());
        // Метки.
        $labels = $request->get("labels", []);
        $product->labels()->sync($labels);
        return redirect()
            ->route("admin.products.show", ["product" => $product])
            ->with("success", "Товар добавлен");
    }

    /**
     * @param $data
     * @param Product $product
     */
    protected function updateValidator($data, Product $product)
    {
        $id = $product->id;
        Validator::make($data, [
            "title" => ["required", "max:250", "unique:products,title,{$id}"],
            "slug" => ["nullable", "max:250", "unique:products,slug,{$id}"],
            "short" => ["nullable", "max:250"],
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
}
