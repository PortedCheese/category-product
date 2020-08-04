<?php

namespace PortedCheese\CategoryProduct\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\ProductLabel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductLabelController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->authorizeResource(ProductLabel::class, "label");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $collection = ProductLabel::query();
        if ($title = $request->get("title", false)) {
            $collection->where("title", "like", "%$title%");
        }
        $collection->orderBy("title");
        $labels = $collection->paginate()->appends($request->input());
        return view(
            "category-product::admin.product-labels.index",
            compact("request", "labels")
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $colors = ProductLabel::getColors();
        return view("category-product::admin.product-labels.create", compact("colors"));
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
        $label = ProductLabel::create($request->all());
        return redirect()
            ->route("admin.product-labels.index")
            ->with("success", "Метка добавлена");
    }

    protected function storeValidator($data)
    {
        Validator::make($data, [
            "title" => ["required", "max:30", "unique:product_labels,title"],
            "slug" => ["nullable", "max:30", "unique:product_labels,slug"],
            "color" => ["required", Rule::in(ProductLabel::getColorKeys())],
        ], [], [
            "title" => "Заголовок",
            "slug" => "Адресная строрка",
            "color" => "Цвет",
        ])->validate();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ProductLabel  $label
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, ProductLabel $label)
    {
        $collection = $label->products()
            ->with("category");
        if ($title = $request->get("title", false)) {
            $collection->where("title", "like", "%$title%");
        }
        $collection->orderBy("title");
        $products = $collection->paginate()->appends($request->input());
        return view(
            "category-product::admin.product-labels.show",
            compact("label", "products", "request")
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ProductLabel  $label
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductLabel $label)
    {
        $colors = ProductLabel::getColors();
        return view("category-product::admin.product-labels.edit", compact("label", "colors"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ProductLabel  $label
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductLabel $label)
    {
        $this->updateValidator($request->all(), $label);
        $label->update($request->all());
        return redirect()
            ->route("admin.product-labels.show", ["label" => $label])
            ->with("success", "Метка успешно обновлена");
    }

    protected function updateValidator($data, ProductLabel $label)
    {
        $id = $label->id;
        Validator::make($data, [
            "title" => ["required", "max:30", "unique:product_labels,title,{$id}"],
            "slug" => ["nullable", "max:30", "unique:product_labels,slug,{$id}"],
            "color" => ["required", Rule::in(ProductLabel::getColorKeys())],
        ], [], [
            "title" => "Заголовок",
            "slug" => "Адресная строрка",
            "color" => "Цвет",
        ])->validate();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProductLabel  $productLabel
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductLabel $label)
    {
        $label->delete();

        return redirect()
            ->route("admin.product-labels.index")
            ->with("success", "Метка успешно удалена");
    }
}
