<?php

namespace PortedCheese\CategoryProduct\Observers;

use App\ProductLabel;

class ProductLabelObserver
{

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
