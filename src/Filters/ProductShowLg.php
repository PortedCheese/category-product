<?php

namespace PortedCheese\CategoryProduct\Filters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image as File;

class ProductShowLg implements FilterInterface {

    public function applyFilter(File $image)
    {
        return $image
            ->resize(385, 385, function ($constraint) {
                $constraint->upsize();
                $constraint->aspectRatio();
            });
    }
}