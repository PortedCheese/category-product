<?php

namespace PortedCheese\CategoryProduct\Filters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image as File;

class CatalogTeaserXs implements FilterInterface {

    public function applyFilter(File $image)
    {
        return $image
            ->resize(543, 543, function ($constraint) {
                $constraint->upsize();
                $constraint->aspectRatio();
            });
    }
}