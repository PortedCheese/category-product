<?php

namespace PortedCheese\CategoryProduct\Models;

use App\ProductVariation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductSpecification extends Model
{
    protected $fillable = [
        "specification_id",
        "product_id",
        "category_id",
        "value",
        "code",
    ];

    /**
     * Характеристика.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function specification()
    {
        return $this->belongsTo(\App\Specification::class);
    }

    /**
     * Вариации с данной характеристикой
     *
     * @return false|BelongsToMany
     */
    public function variations(){
        if (! class_exists(ProductVariation::class)) return false;
        return $this->belongsToMany(ProductVariation::class)->withTimestamps();
    }
    /**
     * Товар.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(\App\Product::class);
    }

    /**
     * Категория.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(\App\Category::class);
    }
}
