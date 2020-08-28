<?php

namespace PortedCheese\CategoryProduct\Listeners;

use PortedCheese\CategoryProduct\Events\CategorySpecificationValuesUpdate;
use PortedCheese\CategoryProduct\Facades\ProductActions;

class CategorySpecificationValuesClearCache
{
    public function handle(CategorySpecificationValuesUpdate $event)
    {
        $category = $event->category;
        ProductActions::forgetCategoryProductIds($category);
        ProductActions::forgetProductSpecificationsValues($category);
    }
}