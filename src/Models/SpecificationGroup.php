<?php

namespace PortedCheese\CategoryProduct\Models;

use Illuminate\Database\Eloquent\Model;
use PortedCheese\BaseSettings\Traits\ShouldSlug;

class SpecificationGroup extends Model
{
    use ShouldSlug;
    
    protected $fillable = [
        "title",
        "slug",
    ];
}
