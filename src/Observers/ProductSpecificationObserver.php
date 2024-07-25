<?php

namespace PortedCheese\CategoryProduct\Observers;

use App\ProductSpecification;
use PortedCheese\CategoryProduct\Facades\ProductActions;

class ProductSpecificationObserver
{
   
    public function updated(ProductSpecification $specification)
    {
        ProductActions::forgetProductAddons($specification->product);
        if (class_exists(\App\ProductVariation::class)){
            product_variation()->clearProductVariationsCache($specification->product);
        }
        $specification->product->clearCache();
    }

    public function deleting(ProductSpecification $specification)
    {
        ProductActions::forgetProductAddons($specification->product);
        if (class_exists(\App\ProductVariation::class)){
            product_variation()->clearProductVariationsCache($specification->product);
            $specification->variations()->detach();
        }
    }
}
