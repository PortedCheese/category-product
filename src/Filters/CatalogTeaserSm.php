<?php

namespace PortedCheese\CategoryProduct\Filters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image as File;

class CatalogTeaserSm implements FilterInterface {

    public function applyFilter(File $image)
    {
        return $image
            ->resize(238, 238, function ($constraint) {
                $constraint->upsize();
                $constraint->aspectRatio();
            });
    }
}