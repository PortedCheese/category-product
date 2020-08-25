<?php

namespace PortedCheese\CategoryProduct\Models;

use App\OrderItem;
use App\ProductVariation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use PortedCheese\BaseSettings\Traits\ShouldGallery;
use PortedCheese\BaseSettings\Traits\ShouldSlug;
use PortedCheese\SeoIntegration\Traits\ShouldMetas;

class Product extends Model
{
    use ShouldSlug, ShouldGallery, ShouldMetas;

    protected $fillable = [
        "title",
        "slug",
        "short",
        "description",
    ];

    protected $metaKey = "products";

    /**
     * Категория товара.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(\App\Category::class);
    }

    /**
     * Метки товара.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function labels()
    {
        return $this->belongsToMany(\App\ProductLabel::class)
            ->withTimestamps();
    }

    /**
     * Занчения.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function specifications()
    {
        return $this->belongsToMany(\App\Specification::class)
            ->withPivot("values", "category_id")
            ->withTimestamps();
    }

    /**
     * Вариации.
     *
     * @return bool|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variations()
    {
        if (class_exists(ProductVariation::class)) {
            return $this->hasMany(ProductVariation::class);
        }
        else {
            return new HasMany($this->newQuery(), $this, "", "");
        }
    }

    /**
     * Позиции заказа.
     *
     * @return HasMany
     */
    public function orderItems()
    {
        if (class_exists(OrderItem::class)) {
            return $this->hasMany(OrderItem::class);
        }
        else {
            return new HasMany($this->newQuery(), $this, "", "");
        }
    }

    /**
     * Данные для тизера.
     *
     * @return mixed
     */
    public function getTeaserData()
    {
        $key = "productTeaserData:{$this->id}";
        $product = $this;
        return Cache::rememberForever($key, function () use ($product) {
            $product->cover;
            $product->labels;
            return $product;
        });
    }

    /**
     * Очистить кэш.
     */
    public function clearCache()
    {
        Cache::forget("productTeaserData:{$this->id}");
    }
}
