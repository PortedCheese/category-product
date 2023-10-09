<?php

namespace PortedCheese\CategoryProduct\Http\Controllers\Site;

use App\Category;
use App\Http\Controllers\Controller;
use App\Meta;
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
            ->whereNotNull("published_at")
            ->orderBy("priority")
            ->get();
        $siteBreadcrumb = [
            (object) [
                'active' => true,
                'url' => route("catalog.categories.index"),
                'title' => config("category-product.catalogPageName"),
            ]
        ];
        $pageMetas = Meta::getByPageKey("catalog");
        return view(
            "category-product::site.categories.index",
            compact("categories", "siteBreadcrumb", "pageMetas")
        );
    }

    /**
     * Просмотр категории.
     *
     * @param Request $request
     * @param Category $category
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     */
    public function show(Request $request, Category $category)
    {
        if ($category->published_at) {
            $collection = $category
                ->children()
                ->whereNotNull("published_at")
                ->orderBy("priority");
            if (config("category-product.subCategoriesPage")) {
                $collection->with("image");
            }
            $categories = $collection->get();

            $siteBreadcrumb = CategoryActions::getSiteBreadcrumb($category);
            $pageMetas = Meta::getByModelKey($category);

            if (config("category-product.subCategoriesPage") && $categories->count()) {
                return view(
                    "category-product::site.categories.index",
                    compact("categories", "category", "siteBreadcrumb", "pageMetas")
                );
            }
            else {
                $products = ProductFilters::filterByCategory($request, $category);
                $productView = Cookie::get("products-view", config("category-product.defaultProductView"));
                $filters = ProductFilters::getFilters($category, true);
                return view(
                    "category-product::site.categories.show",
                    compact("category", "categories", "products", "productView", "filters", "request", "siteBreadcrumb", "pageMetas")
                );
            }
        }
        else
            return redirect(route("catalog.categories.index"));

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
