<?php

namespace PortedCheese\CategoryProduct\Facades;

use Illuminate\Support\Facades\Facade;
use PortedCheese\CategoryProduct\Helpers\CategoryActionsManager;

/**
 * @method static array getTree()
 * @method static bool saveOrder(array $data)
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