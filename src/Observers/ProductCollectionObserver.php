<?php

namespace PortedCheese\CategoryProduct\Observers;

use App\Product;
use App\ProductCollection;
use App\ProductLabel;
use PortedCheese\CategoryProduct\Facades\ProductActions;

class ProductCollectionObserver
{
    /**
     * Очистить кэш товаров при обновлении коллекции.
     * 
     * @param ProductCollection $collection
     */
    public function updated(ProductCollection $collection)
    {
        foreach ($collection->products as $product) {
            /**
             * @var Product $product
             */
            $product->clearCache();
        }
    }

    /**
     * После удаления.
     *
     * @param ProductCollection $collection
     */
    public function deleted(ProductCollection $collection)
    {
        foreach ($collection->products as $product) {
            $collection->products()->detach($product);
            ProductActions::forgetProductCollections($product);
        }
    }
}
