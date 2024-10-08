<?php

namespace PortedCheese\CategoryProduct\Filters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image as File;

class CatalogTeaserXxl implements FilterInterface {

    public function applyFilter(File $image)
    {
        return $image
            ->resize(304, 304, function ($constraint) {
                $constraint->upsize();
                $constraint->aspectRatio();
            });
    }
}