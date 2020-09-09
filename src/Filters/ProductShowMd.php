<?php

namespace PortedCheese\CategoryProduct\Filters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image as File;

class ProductShowMd implements FilterInterface {

    public function applyFilter(File $image)
    {
        return $image
            ->resize(625, 625, function ($constraint) {
                $constraint->upsize();
                $constraint->aspectRatio();
            });
    }
}