<?php

namespace PortedCheese\CategoryProduct\Filters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image as File;

class ProductThumbShow implements FilterInterface {

    public function applyFilter(File $image)
    {
        return $image
            ->resize(65, 65, function ($constraint) {
                $constraint->upsize();
                $constraint->aspectRatio();
            });
    }
}