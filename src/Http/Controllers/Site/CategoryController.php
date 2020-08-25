<?php

namespace PortedCheese\CategoryProduct\Http\Controllers\Site;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use PortedCheese\CategoryProduct\Facades\ProductFilters;

class CategoryController extends Controller
{
    /**
     * Список категорий верхнего уровня.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $categories = Category::query()
            ->with("image")
            ->whereNull("parent_id")
            ->orderBy("priority")
            ->get();
        return view("category-product::site.categories.index", compact("categories"));
    }

    /**
     * Просмотр категории.
     *
     * @param Request $request
     * @param Category $category
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, Category $category)
    {
        $collection = $category
            ->children()
            ->orderBy("priority");

        if (config("category-product.subCategoriesPage")) {
            $categories = $collection->with("image")->get();
            return view(
                "category-product::site.categories.index",
                compact("categories", "category")
            );
        }
        else {
            $categories = $collection->get();
            $products = ProductFilters::filterByCategory($request, $category);
            $productView = Cookie::get("products-view", config("category-product.defaultProductView"));
            return view(
                "category-product::site.categories.show",
                compact("category", "categories", "products", "productView")
            );
        }
    }

    /**
     * Поменять куку.
     *
     * @param Request $request
     * @param string $view
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeProductView(Request $request)
    {
        $view = $request->get("view", "list");
        $cookie = Cookie::make("products-view", $view, 60*24*30);
        Cookie::queue($cookie);
        return response()
            ->json(["success" => true]);
    }
}
