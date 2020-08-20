<?php

namespace PortedCheese\CategoryProduct\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        return view("category-product::site.products.show", compact("product"));
    }
}
