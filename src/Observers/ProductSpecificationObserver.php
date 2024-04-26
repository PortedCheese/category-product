<?php

namespace PortedCheese\CategoryProduct\Observers;

use App\ProductSpecification;

class ProductSpecificationObserver
{
   
    public function updated(ProductSpecification $specification)
    {
        if (class_exists(\App\ProductVariation::class)){
            \PortedCheese\ProductVariation\Facades\ProductVariationActions::clearProductVariationsCache($specification->product);
        }
        $specification->product->clearCache();
    }

    public function deleting(ProductSpecification $specification)
    {
        if (class_exists(\App\ProductVariation::class)){
            $specification->variations()->detach();
        }
    }
}
