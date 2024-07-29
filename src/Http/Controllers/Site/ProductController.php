<?php

namespace PortedCheese\CategoryProduct\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Meta;
use App\Product;
use App\ProductLabel;
use Illuminate\Http\Request;
use PortedCheese\CategoryProduct\Facades\CategoryActions;
use PortedCheese\CategoryProduct\Facades\ProductActions;
use PortedCheese\CategoryProduct\Facades\ProductFavorite;

class ProductController extends Controller
{
    /**
     * Просмотр товара.
     * 
     * @param Product $product
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Product $product)
    {
        if ($product->addonType)
            return redirect(route("catalog.categories.show",["category"=>$product->category]),"301");
        $category = $product->category;
        $gallery = $product->images()->orderBy("weight")->get();
        $siteBreadcrumb = CategoryActions::getSiteBreadcrumb($category, true);
        $pageMetas = Meta::getByModelKey($product);
        $groups = ProductActions::getProductSpecificationsByGroups($product);
        $watch = ProductActions::getYouWatch($product);
        $addonsArray = ProductActions::getProductAddons($product);
        return view(
            "category-product::site.products.show",
            compact("product", "siteBreadcrumb", "gallery", "groups", "watch", "addonsArray", "pageMetas")
        );
    }

    /**
     * Товары по метке.
     *
     * @param Request $request
     * @param ProductLabel $label
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function label(Request $request, ProductLabel $label)
    {
        $perPage = config("category-product.categoryProductsPerPage");
        $products = $label
            ->products()
            ->select("id", "slug")
            ->whereNotNull("published_at")
            ->orderBy("title")
            ->paginate($perPage)
            ->appends($request->input());
        return view("category-product::site.products.label", compact("request", "label", "products"));
    }

    /**
     * Избранное.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function favoriteList(Request $request)
    {
        $perPage = config("category-product.categoryProductsPerPage");
        $actualFavorite = ProductFavorite::getActualFavorite();

        $products = Product::query()
            ->select("id", "slug")
            ->whereIn("id", $actualFavorite)
            ->orderBy("title")
            ->paginate($perPage)
            ->appends($request->input());
        return view("category-product::site.products.favorite", compact("request", "products"));
    }

    /**
     * Добавить в избранное.
     *
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToFavorite(Request $request, Product $product)
    {
        $favorite = ProductFavorite::addToFavorite($product);
        return response()->json(["success" => true, "items" => $favorite]);
    }

    /**
     * Удалить из избранного.
     *
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeFromFavorite(Request $request, Product $product)
    {
        $favorite = ProductFavorite::removeFromFavorite($product);
        return response()->json(["success" => true, "items" => $favorite]);
    }
}
