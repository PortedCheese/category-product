<?php


namespace PortedCheese\CategoryProduct\Helpers;


use App\Category;
use App\Specification;
use App\SpecificationGroup;
use Illuminate\Support\Facades\Cache;
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

    /**
     * Характеристики категории.
     *
     * @param Category $category
     * @param bool $filter
     * @return array|mixed
     */
    public function getCategorySpecificationsInfo(Category $category, $filter = false)
    {
        $key = "specification-actions-getCategorySpecificationsInfo:{$category->id}";

        $specs = Cache::rememberForever($key, function () use ($category) {
            $fields = [];
            $collection = $category->specifications()->orderBy("priority")->get();
            foreach ($collection as $item) {
                $pivot = $item->pivot;
                $fields[$item->id] = (object) [
                    "id" => $item->id,
                    "title" => $pivot->title,
                    "filter" => $pivot->filter,
                    "type" => $item->type,
                    "slug" => $item->slug,
                    "group_id" => $item->group_id,
                ];
            }
            return $fields;
        });

        if ($specs) {
            return $this->sortSpecificationsForFilter($specs);
        }
        return $specs;
    }

    /**
     * Характеристики для фильтра у категории и подкатегорий.
     *
     * @param Category $category
     * @return mixed
     */
    public function getCategoryChildrenSpecificationsInfo(Category $category)
    {
        $key = "specification-actions-getCategoryChildrenSpecificationsInfo:{$category->id}";
        return Cache::rememberForever($key, function() use ($category) {
            $specs = $this->getCategorySpecificationsInfo($category, true);
            $ids = [];
            foreach ($specs as $spec) {
                $ids[] = $spec->id;
            }
            foreach ($category->children as $child) {
                foreach ($this->getCategoryChildrenSpecificationsInfo($child) as $item) {
                    if (! in_array($item->id, $ids)) {
                        $specs[] = $item;
                        $ids[] = $item->id;
                    }
                }
            }
            return $specs;
        });
    }

    /**
     * Забыть кэш информации о характеристиках категории.
     *
     * @param Category $category
     */
    public function forgetCategorySpecificationsInfo(Category $category)
    {
        Cache::forget("specification-actions-getCategorySpecificationsInfo:{$category->id}");
    }

    /**
     * Забыть кэш информации о характеристиках подкатегорий.
     *
     * @param Category $category
     */
    public function forgetCategoryChildrenSpecificationInfo(Category $category)
    {
        Cache::forget("specification-actions-getCategoryChildrenSpecificationsInfo:{$category->id}");
        if ($category->parent_id) {
            $parent = $category->parent;
            $this->forgetCategoryChildrenSpecificationInfo($parent);
        }
    }

    /**
     * Получить только характеристики для фильтра.
     *
     * @param array $specs
     * @return array
     */
    protected function sortSpecificationsForFilter(array $specs)
    {
        $filtered = [];
        foreach ($specs as $spec) {
            if ($spec->filter) {
                $filtered[] = $spec;
            }
        }
        return $filtered;
    }
}