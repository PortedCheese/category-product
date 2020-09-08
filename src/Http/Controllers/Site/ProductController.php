<?php

namespace PortedCheese\CategoryProduct\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;
use PortedCheese\CategoryProduct\Facades\CategoryActions;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        $category = $product->category;
        $siteBreadcrumb = CategoryActions::getSiteBreadcrumb($category, true);
        return view("category-product::site.products.show", compact("product", "siteBreadcrumb"));
    }
}
