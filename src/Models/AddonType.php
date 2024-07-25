<?php

namespace PortedCheese\CategoryProduct\Models;

use Illuminate\Database\Eloquent\Model;
use PortedCheese\BaseSettings\Traits\ShouldSlug;

class AddonType extends Model
{
    use ShouldSlug;

    protected $fillable = [
        "title",
        "slug",
        "short",
        "description",
    ];

    /**
     *  Дополнения типа
     *
     * @return mixed
     */
    public function addons()
    {
        return $this->hasMany(\App\Product::class);
    }

}
