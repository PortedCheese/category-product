<?php

namespace PortedCheese\CategoryProduct\Observers;

use App\Product;

class ProductObserver
{
    /**
     * Перед сохранением.
     *
     * @param Product $product
     */
    public function creating(Product $product)
    {
        $product->published_at = now();
    }

    /**
     * После удаления.
     *
     * @param Product $product
     */
    public function deleted(Product $product)
    {
        // Очистить метки.
        $product->labels()->detach();
    }
}
