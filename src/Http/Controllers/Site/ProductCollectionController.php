<?php

namespace PortedCheese\CategoryProduct\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProductCollection;

class ProductCollectionController extends Controller
{
    public function index(Request $request){
        $perPage = config("category-product.categoryProductsPerPage");
        $collections = ProductCollection::query()
        ->whereNotNull("published_at");
        if ($title = $request->get("title", false)) {
            $collections->where("title", "like", "%$title%");
        }
        $collections->orderBy("priority");
        $products = $collections->paginate($perPage)->appends($request->input());

        $siteBreadcrumb[] = (object) [
                "title" => config("category-product.productCollectionsName"),
                "url" => route("catalog.product-collections.index"),
                "active" => true,
            ];

        return view("category-product::site.product-collections.index", compact("request",  "products", "siteBreadcrumb"));
    }

    /**
     * Товары в коллекции.
     *
     * @param Request $request
     * @param ProductCollection $collection
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, ProductCollection $collection)
    {
        if (! $collection->published_at) abort(404);
        $perPage = config("category-product.categoryProductsPerPage");
        $products = $collection
            ->products()
            ->select("id", "slug")
            ->whereNotNull("published_at")
            ->orderBy("title")
            ->paginate($perPage)
            ->appends($request->input());

        $siteBreadcrumb[] = (object) [
            "title" => config("category-product.productCollectionsName"),
            "url" => route("catalog.product-collections.index"),
            "active" => false,
        ];
        $siteBreadcrumb[] = (object) [
            "title" => $collection->title,
            "url" => route("catalog.product-collections.show",["collection" => $collection]),
            "active" => true,
        ];

        return view("category-product::site.product-collections.show", compact("request", "collection", "products", "siteBreadcrumb"));
    }


}
