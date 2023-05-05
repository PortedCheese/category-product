<?php

namespace PortedCheese\CategoryProduct\Models;

use Illuminate\Database\Eloquent\Model;
use PortedCheese\BaseSettings\Traits\ShouldImage;
use PortedCheese\BaseSettings\Traits\ShouldSlug;
use PortedCheese\SeoIntegration\Traits\ShouldMetas;

class Category extends Model
{
    use ShouldSlug, ShouldImage, ShouldMetas;
    
    protected $fillable = [
        "title",
        "slug",
        "short",
        "description",
    ];

    protected $metaKey = "categories";

    /**
     * Родительская категолрия.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(\App\Category::class, "parent_id");
    }

    /**
     * Дочернии категории.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(\App\Category::class, "parent_id");
    }

    /**
     * Характеристики.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function specifications()
    {
        return $this->belongsToMany(\App\Specification::class)
            ->withPivot("title")
            ->withPivot("filter")
            ->withPivot("priority")
            ->withTimestamps();
    }

    /**
     * Товары категории.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(\App\Product::class);
    }

    /**
     * Уровень вложенности.
     * 
     * @return int
     */
    public function getNestingAttribute()
    {
        if (empty($this->parent_id)) {
            return 1;
        }
        return $this->parent->nesting + 1;
    }
}
