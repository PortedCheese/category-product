<?php

namespace PortedCheese\CategoryProduct\Filters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image as File;

class ProductShowSm implements FilterInterface {

    public function applyFilter(File $image)
    {
        return $image
            ->resize(465, 465, function ($constraint) {
                $constraint->upsize();
                $constraint->aspectRatio();
            });
    }
}