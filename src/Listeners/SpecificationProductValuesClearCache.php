<?php

namespace PortedCheese\CategoryProduct\Listeners;

use PortedCheese\CategoryProduct\Events\CategoryChangePosition;
use PortedCheese\CategoryProduct\Facades\CategoryActions;

class SpecificationProductValuesClearCache
{
    public function handle(CategoryChangePosition $event)
    {
        $category = $event->category;
        // Очистить список id категорий.
        CategoryActions::forgetCategoryChildrenIdsCache($category);
    }
}