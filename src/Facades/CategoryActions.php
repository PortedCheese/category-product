<?php

namespace PortedCheese\CategoryProduct\Facades;

use App\Category;
use Illuminate\Support\Facades\Facade;
use PortedCheese\CategoryProduct\Helpers\CategoryActionsManager;

/**
 * @method static array getTree()
 * @method static bool saveOrder(array $data)
 * @method static syncSpec(Category $category)
 * @method static copyParentSpec(Category $category, Category $customParent = null)
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