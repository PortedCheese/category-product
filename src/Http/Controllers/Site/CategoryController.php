<?php

namespace PortedCheese\CategoryProduct\Http\Controllers\Site;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        $categories = $category
            ->children()
            ->with("image")
            ->orderBy("priority")
            ->get();

        if (config("category-product.subCategoriesPage")) {
            return view(
                "category-product::site.categories.index",
                compact("categories", "category")
            );
        }
        else {
            $products = ProductFilters::filterByCategory($request, $category);
            return view(
                "category-product::site.categories.show",
                compact("category", "categories")
            );
        }
    }
}