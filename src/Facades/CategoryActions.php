<?php

namespace PortedCheese\CategoryProduct\Facades;

use App\Category;
use Illuminate\Support\Facades\Facade;
use PortedCheese\CategoryProduct\Helpers\CategoryActionsManager;

/**
 * @method static array getTree()
 * @method static bool saveOrder(array $data)
 * @method static syncSpec(Category $category)
 * @nethod static copyParentSpec(Category $category, Category $customParent = null)
 * 
 * @package PortedCheese\CategoryProduct\Facade
 * @see CategoryActionsManager
 */
class CategoryActions extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "category-actions";
    }
}