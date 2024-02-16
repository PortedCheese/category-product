<?php

namespace PortedCheese\CategoryProduct\Models;

use Illuminate\Database\Eloquent\Model;
use PortedCheese\BaseSettings\Traits\ShouldImage;
use PortedCheese\BaseSettings\Traits\ShouldSlug;
use PortedCheese\SeoIntegration\Traits\ShouldMetas;

class ProductCollection extends Model
{
    use ShouldSlug, ShouldImage, ShouldMetas;

    protected $fillable = [
        "title",
        "slug",
        "short",
        "description",
    ];

    protected $metaKey = "product_collection";

    /**
     * Товары по метке.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(\App\Product::class)
            ->withTimestamps();
    }

}
