<?php

namespace PortedCheese\CategoryProduct\Listeners;

use App\Product;
use PortedCheese\BaseSettings\Events\ImageUpdate;

class ProductGalleryChange
{
    public function handle(ImageUpdate $event)
    {
        $morph = $event->morph;
        if (! empty($morph) && get_class($morph) == Product::class) {
            $morph->updated_at = now();
            $morph->save();
        }
    }
}