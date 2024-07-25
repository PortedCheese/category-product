<?php

namespace PortedCheese\CategoryProduct\Listeners;

use PortedCheese\CategoryProduct\Events\CategoryChangePosition;
use PortedCheese\CategoryProduct\Facades\CategoryActions;
use PortedCheese\ProductVariation\Facades\ProductVariationActions;

class SpecificationProductValuesClearCache
{
    public function handle(CategoryChangePosition $event)
    {
        $category = $event->category;
        // Очистить список id категорий.
        CategoryActions::forgetCategoryChildrenIdsCache($category);
        if (class_exists(\App\ProductVariation::class) && config("product-variation.enablePriceFilter")) {
            ProductVariationActions::clearPricesCache($category);
        }
    }
}