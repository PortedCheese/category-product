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
    ];

    protected $metaKey = "categories";

    protected static function booted()
    {
        parent::booted();

        static::creating(function (\App\Category $model) {
            $max = \App\Category::query()
                ->where("parent_id", $model->parent_id)
                ->max("priority");
            $model->priority = $max + 1;
        });
    }

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
}