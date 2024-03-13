<?php

namespace PortedCheese\CategoryProduct\Facades;

use App\Category;
use App\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Facade;
use PortedCheese\CategoryProduct\Helpers\ProductActionsManager;

/**
 * @method static array|Collection getProductSpecifications(Product $product, bool $collection = false)
 * @method static array getProductSpecificationsByGroups(Product $product, Collection $collection = null)
 * @method static array|Collection getAvailableSpecifications(Product $product, bool $collection = false)
 * @method static changeCategory(Product $product, int $categoryId)
 *
 * @method static array getProductSpecificationValues(Category $category, $includeSubs = false)
 * @method static forgetProductSpecificationsValues(Category $category)
 * @method static array getCategoryProductIds(Category $category, $includeSubs = false)
 * @method static forgetCategoryProductIds(Category $category)
 * @method static mixed getProductCollections(Product $product)
 * @method static forgetProductCollections(Product $product)
 *
 * @method static array getYouWatch(Product $product)
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