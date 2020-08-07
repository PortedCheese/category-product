<?php


namespace PortedCheese\CategoryProduct\Helpers;


use App\Category;
use App\Specification;
use App\SpecificationGroup;
use Illuminate\Support\Facades\DB;

class SpecificationActionManager
{
    /**
     * Получить группы.
     * 
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getGroups()
    {
        return SpecificationGroup::query()
            ->orderBy("priority")
            ->get();
    }

    /**
     * Существующие характеристики, которые можно добавить для категории.
     * 
     * @param Category $category
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableForCategory(Category $category)
    {
        $ids = [];
        foreach ($category->specifications as $specification) {
            $ids[] = $specification->id;
        }
        return Specification::query()->whereNotIn("id", $ids)->get();
    }

    /**
     * Количество значений.
     *
     * @param Category $category
     * @param Specification $specification
     * @return int
     */
    public function checkProductsSpecifications(Category $category, Specification $specification)
    {
        return DB::table("product_specification")
            ->where("specification_id", $specification->id)
            ->where("category_id", $category->id)
            ->count();
    }
}