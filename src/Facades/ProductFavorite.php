<?php

namespace PortedCheese\CategoryProduct\Facades;

use App\Product;
use Illuminate\Support\Facades\Facade;
use PortedCheese\CategoryProduct\Helpers\ProductFavoriteManager;

/**
 * @method static array addToFavorite(Product $product)
 * @method static array removeFromFavorite(Product $product)

 * @method static array getCurrentFavorite()
 * @method static array getActualFavorite()
 *
 * @see ProductFavoriteManager
 */
class ProductFavorite extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "product-favorite";
    }
}
