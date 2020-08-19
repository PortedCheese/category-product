<?php

namespace PortedCheese\CategoryProduct\Http\Controllers\Site;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        $children = $category
            ->children()
            ->orderBy("priority")
            ->get();

        return view(
            "category-product::site.categories.show",
            compact("category", "children")
        );
    }
}
