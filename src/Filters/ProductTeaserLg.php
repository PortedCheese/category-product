<?php

namespace PortedCheese\CategoryProduct\Filters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image as File;

class ProductTeaserLg implements FilterInterface {

    public function applyFilter(File $image)
    {
        return $image
            ->resize(208, 208, function ($constraint) {
                $constraint->upsize();
                $constraint->aspectRatio();
            });
    }
}