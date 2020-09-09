<?php

namespace PortedCheese\CategoryProduct\Filters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image as File;

class ProductThumbSimpleShow implements FilterInterface {

    public function applyFilter(File $image)
    {
        return $image
            ->fit(65, 65);
    }
}