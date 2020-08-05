<?php

namespace PortedCheese\CategoryProduct\Facades;

use App\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Facade;
use PortedCheese\CategoryProduct\Helpers\SpecificationActionManager;

/**
 * @method static Collection getGroups()
 * @method static Collection getAvailableForCategory(Category $category)
 * 
 * @package PortedCheese\CategoryProduct\Facade
 * @see SpecificationActionManager
 */
class SpecificationActions extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "specification-actions";
    }
}