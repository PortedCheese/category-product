<?php

namespace PortedCheese\CategoryProduct\Facades;

use App\Category;
use App\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Facade;
use PortedCheese\CategoryProduct\Helpers\ProductFilterManager;
use Illuminate\Http\Request;

/**
 * @method static filterByCategory(Request $request, Category $category)
 *
 * @see ProductFilterManager
 */
class ProductFilters extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "product-filters";
    }
}