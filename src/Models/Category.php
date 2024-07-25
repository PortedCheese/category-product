<?php

namespace PortedCheese\CategoryProduct\Models;

use Illuminate\Database\Eloquent\Model;
use PortedCheese\BaseSettings\Traits\ShouldImage;
use PortedCheese\BaseSettings\Traits\ShouldSlug;
use PortedCheese\CategoryProduct\Events\CategorySpecificationValuesUpdate;
use PortedCheese\CategoryProduct\Events\ProductListChange;
use PortedCheese\CategoryProduct\Facades\CategoryActions;
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
        return $this->hasMany(\App\Product::class)->whereNull('addon_type_id');
    }

    /**
     * Дополнения категории.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|null
     */
    public function addons()
    {
        if (config("category-product.useAddons", false))
            return $this->hasMany(\App\Product::class)->whereNotNull('addon_type_id');
        else
            return null;
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



    /**
     * Change publish status all children and products
     *
     */
    public function publishCascade()
    {
        $children = $this->children;
        $parentPublished = $this->isParentPublished();

        if ($parentPublished){
            // change publish status
            $this->publish();
            if(! $this->published_at){
                CategoryActions::runParentEvents($this);
                CategoryActions::unPublishChildren($children);
                CategoryActions::unPublishChildren($this->products, false);
            }
            return true;
        }
        else
        {
            if (! $this->published_at){
                return false;
            }
            else {
                $this->publish();
                CategoryActions::unPublishChildren($children);
                CategoryActions::unPublishChildren($this->products, false);
                return true;
            }
        }


    }

    /**
     * Get parent publish status
     *
     * @return \Illuminate\Support\Carbon|mixed
     */

    public function isParentPublished(){

        $parent = $this->parent;
        return $parent ? $parent->published_at : now();

    }

    /**
     * Change publish status
     *
     */
    protected function publish()
    {
        $this->published_at = $this->published_at  ? null : now();
        $this->save();
    }


}
