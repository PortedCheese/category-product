<?php

namespace PortedCheese\CategoryProduct\Listeners;

use App\Product;
use PortedCheese\BaseSettings\Events\ImageUpdate;

class ProductGalleryChange
{
    protected $ids;

    public function __construct()
    {
        $this->ids = [];
    }

    public function handle(ImageUpdate $event)
    {
        $morph = $event->morph;
        if (get_class($morph) == Product::class) {
            $morph->updated_at = now();
            $morph->save();
        }
    }
}