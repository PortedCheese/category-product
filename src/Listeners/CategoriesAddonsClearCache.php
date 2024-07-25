<?php

namespace PortedCheese\CategoryProduct\Listeners;

use App\Product;
use PortedCheese\CategoryProduct\Events\CategoriesAddonsUpdate;
use PortedCheese\CategoryProduct\Facades\CategoryActions;
use PortedCheese\CategoryProduct\Facades\ProductActions;

class CategoriesAddonsClearCache
{
    public function handle(CategoriesAddonsUpdate $event)
    {
        $category = $event->category;
        $cIds = CategoryActions::getCategoryChildren($category,true);
        $products = Product::query()->whereNull("addon_type_id")->whereIn("category_id", $cIds)->get();
        foreach ($products as $product){
            ProductActions::forgetProductAddons($product);
        }
    }
}