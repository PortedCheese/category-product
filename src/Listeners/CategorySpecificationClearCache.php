<?php

namespace PortedCheese\CategoryProduct\Listeners;

use PortedCheese\CategoryProduct\Events\CategorySpecificationUpdate;
use PortedCheese\CategoryProduct\Facades\SpecificationActions;

class CategorySpecificationClearCache
{
    public function handle(CategorySpecificationUpdate $event)
    {
        $category = $event->category;
        // Очистить кэш.
        SpecificationActions::forgetCategorySpecificationsInfo($category);
        SpecificationActions::forgetCategoryChildrenSpecificationInfo($category);
    }
}