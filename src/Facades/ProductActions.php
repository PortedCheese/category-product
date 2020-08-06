<?php

namespace PortedCheese\CategoryProduct\Facades;

use App\Product;
use Illuminate\Support\Facades\Facade;
use PortedCheese\CategoryProduct\Helpers\ProductActionsManager;

/**
 * @method static changeCategory(Product $product, int $categoryId)
 *
 * @see ProductActionsManager
 */
class ProductActions extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "product-actions";
    }
}