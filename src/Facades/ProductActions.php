<?php

namespace PortedCheese\CategoryProduct\Facades;

use App\Category;
use App\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Facade;
use PortedCheese\CategoryProduct\Helpers\ProductActionsManager;

/**
 * @method static changeCategory(Product $product, int $categoryId)
 * @method static array|Collection getAvailableSpecifications(Product $product, bool $collection = false)
 * @method static array|Collection getProductSpecifications(Product $product, bool $collection = false)
 *
 * @method static array getProductSpecificationValues(Category $category, $includeSubs = false)
 * @method static forgetProductSpecificationsValues(Category $category)
 * @method static array getCategoryProductIds(Category $category, $includeSubs = false)
 * @method static forgetCategoryProductIds(Category $category)
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