<?php

namespace PortedCheese\CategoryProduct\Filters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image as File;

class CatalogTeaserLg implements FilterInterface {

    public function applyFilter(File $image)
    {
        return $image
            ->resize(288, 288, function ($constraint) {
                $constraint->upsize();
                $constraint->aspectRatio();
            });
    }
}