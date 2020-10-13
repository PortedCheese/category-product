<?php

namespace PortedCheese\CategoryProduct\Observers;

use App\Product;
use App\ProductLabel;

class ProductLabelObserver
{
    /**
     * Очистить кэш товаров при обновлении метки.
     * 
     * @param ProductLabel $label
     */
    public function updated(ProductLabel $label)
    {
        foreach ($label->products as $product) {
            /**
             * @var Product $product
             */
            $product->clearCache();
        }
    }

    /**
     * После удаления.
     *
     * @param ProductLabel $label
     */
    public function deleted(ProductLabel $label)
    {
        foreach ($label->products as $product) {
            $label->products()->detach($product);
        }
    }
}
