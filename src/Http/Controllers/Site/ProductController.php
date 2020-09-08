<?php

namespace PortedCheese\CategoryProduct\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Product;
use App\ProductLabel;
use Illuminate\Http\Request;
use PortedCheese\CategoryProduct\Facades\CategoryActions;

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
        $category = $product->category;
        $siteBreadcrumb = CategoryActions::getSiteBreadcrumb($category, true);
        return view("category-product::site.products.show", compact("product", "siteBreadcrumb"));
    }

    public function label(Request $request, ProductLabel $label)
    {
        $perPage = config("category-product.categoryProductsPerPage");
        $products = $label
            ->products()
            ->select("id", "slug")
            ->orderBy("title")
            ->paginate($perPage)
            ->appends($request->input());
        return view("category-product::site.products.label", compact("request", "label", "products"));
    }
}
