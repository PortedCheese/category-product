<?php

namespace PortedCheese\CategoryProduct\Events;

use App\Product;
use Illuminate\Queue\SerializesModels;

class ProductListChange
{
    use SerializesModels;

    public $product;

    /**
     * Create a new event instance.
     *
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }
}
