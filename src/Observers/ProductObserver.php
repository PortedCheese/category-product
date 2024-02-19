<?php

namespace PortedCheese\CategoryProduct\Observers;

use App\Product;
use PortedCheese\CategoryProduct\Events\CategorySpecificationValuesUpdate;

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

    public function created(Product $product)
    {
        // При добавлении товара меняется список идентификаторов товаров в категории.
        $category = $product->category;
        event(new CategorySpecificationValuesUpdate($category));
    }

    /**
     * После обновления.
     *
     * @param Product $product
     */
    public function updated(Product $product)
    {
        $product->clearCache();
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
        /// Очистить коллекции
        $product->collections()->detach();
        // Очистить кэш.
        $product->clearCache();
        // При удалении товара меняется список идентификаторов товаров в категории и их значений.
        $category = $product->category;
        event(new CategorySpecificationValuesUpdate($category));

        // Очистить значения полей.
        foreach ($product->specifications as $specification) {
            $specification->delete();
        }
    }
}
