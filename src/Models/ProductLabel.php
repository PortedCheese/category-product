<?php

namespace PortedCheese\CategoryProduct\Models;

use Illuminate\Database\Eloquent\Model;
use PortedCheese\BaseSettings\Traits\ShouldSlug;

class ProductLabel extends Model
{
    const COLORS = [
        "dark" => "Темный",
        "yellow" => "Желтый",
        "green" => "Зеленый",
        "red" => "Красный",
        "purple" => "Фиолетовый",
        "blue" => "Синий",
        "orange" => "Оранжевый",
        "pink" => "Розовый",
        "burgundy" => "Бордовый",
        "gray" => "Серый",
    ];

    use ShouldSlug;

    protected $fillable = [
        "title",
        "slug",
        "color",
    ];

    protected static function booted()
    {
        parent::booted();

        static::deleting(function (\App\ProductLabel $model) {
            foreach ($model->products as $product) {
                $model->products()->detach($product);
            }
        });
    }

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

    /**
     * Имя цвета.
     *
     * @return string
     */
    public function getColorNameAttribute()
    {
        $key = $this->color;
        if (! empty(\App\ProductLabel::COLORS[$key])) {
            return \App\ProductLabel::COLORS[$key];
        }
        else {
            return "Не определено";
        }
    }

    /**
     * Получить цвета.
     *
     * @return array
     */
    public static function getColors()
    {
        $colors = [];
        foreach (\App\ProductLabel::COLORS as $key => $value) {
            $colors[] = (object) [
                "key" => $key,
                "title" => $value,
            ];
        }
        return $colors;
    }

    /**
     * Ключи цветов.
     * 
     * @return array
     */
    public static function getColorKeys()
    {
        return array_keys(\App\ProductLabel::COLORS);
    }
}
