<?php

namespace PortedCheese\CategoryProduct\Listeners;

use PortedCheese\CategoryProduct\Events\CategoryChangePosition;
use PortedCheese\CategoryProduct\Events\CategorySpecificationValuesUpdate;
use PortedCheese\CategoryProduct\Facades\CategoryActions;

class CategoryIdsInfoClearCache
{
    public function handle(CategoryChangePosition $event)
    {
        $category = $event->category;
        // Очистить список id категорий.
        CategoryActions::forgetCategoryChildrenIdsCache($category);
        event(new CategorySpecificationValuesUpdate($category));
    }
}