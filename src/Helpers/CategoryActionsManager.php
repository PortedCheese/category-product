<?php

namespace PortedCheese\CategoryProduct\Helpers;

use App\Category;
use App\Specification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use PortedCheese\CategoryProduct\Events\CategoryFieldUpdate;

class CategoryActionsManager
{
    /**
     * Список всех категорий.
     *
     * @return array
     */
    public function getAllList()
    {
        $categories = [];
        foreach (Category::all()->sortBy("title") as $item) {
            $categories[$item->id] = "$item->title ({$item->slug})";
        }
        return $categories;
    }

    /**
     * Получить дерево категорий.
     * 
     * @param bool $forJs
     * @return array
     */
    public function getTree()
    {
        list($tree, $noParent) = $this->makeTreeDataWithNoParent();
        $this->addChildren($tree);
        $this->clearTree($tree, $noParent);
        return $this->sortByPriority($tree);
    }

    /**
     * Сохранить порядок.
     *
     * @param array $data
     * @return bool
     */
    public function saveOrder(array $data)
    {
        try {
            $this->setItemsWeight($data, 0);
        }
        catch (\Exception $exception) {
            return false;
        }
        return true;
    }

    /**
     * Синхронизировать характеристики у дочерних.
     *
     * @param Category $category
     */
    public function syncSpec(Category $category)
    {
        foreach ($category->children as $child) {
            /**
             * @var Category $child
             */
            $this->copyParentSpec($child);
            $this->syncSpec($child);
            event(new CategoryFieldUpdate($child));
        }
    }

    /**
     * Скопировать характеристики родительской категории.
     *
     * @param Category $category
     * @param Category|null $customParent
     */
    public function copyParentSpec(Category $category, Category $customParent = null)
    {
        if (! $customParent) {
            if (! $parent = $category->parent) {
                return;
            }
        }
        else {
            $parent = $customParent;
        }
        /**
         * @var Category $parent
         */
        $parentSpecs = $parent->specifications;
        if (! $parentSpecs->count()) {
            return;
        }
        $ids = [];
        foreach ($category->specifications as $specification) {
            $ids[] = $specification->id;
        }
        foreach ($parentSpecs as $item) {
            /**
             * @var Specification $item
             */
            $pivot = $item->pivot;
            if (empty($pivot)) {
                continue;
            }
            $data = [
                "title" => $pivot->title,
                "filter" => $pivot->filter,
                "priority" => $pivot->priority,
            ];
            if (in_array($item->id, $ids)) {
                $item->categories()
                    ->updateExistingPivot($category, $data);
            }
            else {
                $item->categories()
                    ->attach($category, $data);
            }
        }
    }

    /**
     * Сохранить порядок.
     *
     * @param array $items
     * @param int $parent
     */
    protected function setItemsWeight(array $items, int $parent)
    {
        foreach ($items as $priority => $item) {
            $id = $item["id"];
            if (! empty($item["children"])) {
                $this->setItemsWeight($item["children"], $id);
            }
            $parentId = ! empty($parent) ? $parent : null;
            DB::table("categories")
                ->where("id", $id)
                ->update(["priority" => $priority, "parent_id" => $parentId]);
        }
    }

    /**
     * Сортировка результата.
     *
     * @param $tree
     * @return array
     */
    protected function sortByPriority($tree)
    {
        $sorted = array_values(Arr::sort($tree, function ($value) {
            return $value["priority"];
        }));
        foreach ($sorted as &$item) {
            if (! empty($item["children"])) {
                $item["children"] = self::sortByPriority($item["children"]);
            }
        }
        return $sorted;
    }

    /**
     * Очистить дерево от дочерних.
     *
     * @param $tree
     * @param $noParent
     */
    protected function clearTree(&$tree, $noParent)
    {
        foreach ($noParent as $id) {
            $this->removeChildren($tree, $id);
        }
    }

    /**
     * Убрать подкатегории.
     *
     * @param $tree
     * @param $id
     */
    protected function removeChildren(&$tree, $id)
    {
        if (empty($tree[$id])) {
            return;
        }
        $item = $tree[$id];
        foreach ($item["children"] as $key => $child) {
            $this->removeChildren($tree, $key);
            if (! empty($tree[$key])) {
                unset($tree[$key]);
            }
        }
    }

    /**
     * Добавить дочернии элементы.
     *
     * @param $tree
     */
    protected function addChildren(&$tree)
    {
        foreach ($tree as $id => $item) {
            if (empty($item["parent"])) {
                continue;
            }
            $this->addChild($tree, $item, $id);
        }
    }

    /**
     * Добавить дочерний элемент.
     *
     * @param $tree
     * @param $item
     * @param $id
     * @param bool $children
     */
    protected function addChild(&$tree, $item, $id, $children = false)
    {
        // Добавление к дочерним.
        if (! $children) {
            $tree[$item["parent"]]["children"][$id] = $item;
        }
        // Обновление дочерних.
        else {
            $tree[$item["parent"]]["children"][$id]["children"] = $children;
        }

        $parent = $tree[$item["parent"]];
        if (! empty($parent["parent"])) {
            $items = $parent["children"];
            $this->addChild($tree, $parent, $parent["id"], $items);
        }
    }

    /**
     * Получить данные по категориям.
     *
     * @return array
     */
    protected function makeTreeDataWithNoParent()
    {
        $categories = DB::table("categories")
            ->select("id", "title", "slug", "parent_id", "priority")
            ->orderBy("parent_id")
            ->get();

        $tree = [];
        $noParent = [];
        foreach ($categories as $category) {
            $tree[$category->id] = [
                "title" => $category->title,
                'slug' => $category->slug,
                'parent' => $category->parent_id,
                "priority" => $category->priority,
                "id" => $category->id,
                'children' => [],
                "url" => route(config("category-product.categoryShowRouteName"), ['category' => $category->slug])
            ];
            if (empty($category->parent_id)) {
                $noParent[] = $category->id;
            }
        }
        return [$tree, $noParent];
    }
}