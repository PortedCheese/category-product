<?php

namespace PortedCheese\CategoryProduct\Filters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image as File;

class ProductSimpleShowMd implements FilterInterface {

    public function applyFilter(File $image)
    {
        return $image
            ->fit(625, 625);
    }
}