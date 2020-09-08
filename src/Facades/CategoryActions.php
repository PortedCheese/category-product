<?php

namespace PortedCheese\CategoryProduct\Facades;

use App\Category;
use Illuminate\Support\Facades\Facade;
use phpDocumentor\Reflection\Types\Static_;
use PortedCheese\CategoryProduct\Helpers\CategoryActionsManager;

/**
 * @method static array getCategoryChildren(Category $category, $includeSelf = false)
 * @method static forgetCategoryChildrenIdsCache(Category $category)
 * @method static array getSiteBreadcrumb(Category $category, $isProductPage = false, $parent = false)
 * @method static array getAdminBreadcrumb(Category $category, $isProductPage = false)
 * @method static array getTree()
 * @method static bool saveOrder(array $data)
 * @method static syncSpec(Category $category)
 * @method static copyParentSpec(Category $category, Category $customParent = null)
 * @method static array getAllList()
 *
 * @see CategoryActionsManager
 */
class CategoryActions extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "category-actions";
    }
}