<?php

namespace PortedCheese\CategoryProduct\Models;

use Illuminate\Database\Eloquent\Model;
use PortedCheese\BaseSettings\Traits\ShouldSlug;

class Specification extends Model
{
    use ShouldSlug;

    const TYPES = [
        "select" => "Список",
        "checkbox" => "Чекбокс",
        "range" => "Диапазон",
    ];

    protected $fillable = [
        "title",
        "slug",
        "type",
        "group_id",
    ];

    /**
     * Получить список типов.
     *
     * @return array
     */
    public static function getTypes()
    {
        $array = [];
        foreach (\App\Specification::TYPES as $key => $title) {
            $array[] = (object) [
                "key" => $key,
                "title" => $title,
            ];
        }
        return $array;
    }

    /**
     * Название типа.
     *
     * @param $key
     * @return mixed
     */
    public static function getTypeByKey($key)
    {
        if (! empty(self::TYPES[$key])) {
            return self::TYPES[$key];
        }
        else {
            return $key;
        }
    }

    /**
     * Категории.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(\App\Category::class)
            ->withPivot("title")
            ->withPivot("filter")
            ->withPivot("priority")
            ->withTimestamps();
    }

    /**
     * Значения полей.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(\App\ProductSpecification::class);
    }

    /**
     * Группа.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(\App\SpecificationGroup::class, "group_id");
    }

    /**
     * Тип поля.
     * 
     * @return mixed
     */
    public function getTypeHumanAttribute()
    {
        $type = $this->type;
        return self::getTypeByKey($type);
    }

    /**
     * Если поле больше нигде не используется, удаляем.
     *
     * @throws \Exception
     */
    public function checkCategoryOnDetach()
    {
        if (! $this->categories->count()) {
            $this->delete();
        }
    }
}
