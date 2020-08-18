<?php

namespace PortedCheese\CategoryProduct\Facades;

use App\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Facade;
use PortedCheese\CategoryProduct\Helpers\ProductActionsManager;

/**
 * @method static changeCategory(Product $product, int $categoryId)
 * @method static array|Collection getAvailableSpecifications(Product $product, bool $collection = false)
 * @method static array|Collection getProductSpecifications(Product $product, bool $collection = false)
 *
 * @see ProductActionsManager
 */
class ProductActions extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "product-actions-pkg";
    }
}