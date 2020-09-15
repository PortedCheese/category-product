<?php

namespace PortedCheese\CategoryProduct\Http\Controllers\Site;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use PortedCheese\CategoryProduct\Facades\CategoryActions;
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
        $siteBreadcrumb = [
            (object) [
                'active' => true,
                'url' => route("catalog.categories.index"),
                'title' => config("category-product.catalogPageName"),
            ]
        ];
        return view("category-product::site.categories.index", compact("categories", "siteBreadcrumb"));
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
            $collection->with("image");
        }
        $categories = $collection->get();

        $siteBreadcrumb = CategoryActions::getSiteBreadcrumb($category);

        if (config("category-product.subCategoriesPage") && $categories->count()) {
            return view(
                "category-product::site.categories.index",
                compact("categories", "category", "siteBreadcrumb")
            );
        }
        else {
            $products = ProductFilters::filterByCategory($request, $category);
            $productView = Cookie::get("products-view", config("category-product.defaultProductView"));
            $filters = ProductFilters::getFilters($category, true);
            return view(
                "category-product::site.categories.show",
                compact("category", "categories", "products", "productView", "filters", "request", "siteBreadcrumb")
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
