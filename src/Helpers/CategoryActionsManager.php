<?php

namespace PortedCheese\CategoryProduct\Helpers;

use App\Category;
use App\Specification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use PortedCheese\CategoryProduct\Events\CategorySpecificationUpdate;

class CategoryActionsManager
{
    /**
     * Получить id всех подкатегорий.
     *
     * @param Category $category
     * @param bool $includeSelf
     * @return array
     */
    public function getCategoryChildren(Category $category, $includeSelf = false)
    {
        $key = "category-actions-getCategoryChildren:{$category->id}";
        $children = Cache::rememberForever($key, function () use ($category) {
            $children = [];
            $collection = Category::query()
                ->select("id")
                ->where("parent_id", $category->id)
                ->get();
            foreach ($collection as $child) {
                $children[] = $child->id;
                $categories = $this->getCategoryChildren($child);
                if (! empty($categories)) {
                    foreach ($categories as $id) {
                        $children[] = $id;
                    }
                }
            }
            return $children;
        });
        if ($includeSelf) {
            $children[] = $category->id;
        }
        return $children;
    }

    /**
     * Очистить кэш списка id категорий.
     *
     * @param Category $category
     */
    public function forgetCategoryChildrenIdsCache(Category $category)
    {
        Cache::forget("category-actions-getCategoryChildren:{$category->id}");
        $parent = $category->parent;
        if (! empty($parent)) {
            $this->forgetCategoryChildrenIdsCache($parent);
        }
    }

    /**
     * Хлебные крошки для сайта.
     *
     * @param Category $category
     * @param bool $isProductPage
     * @param bool $parent
     * @return array
     */
    public function getSiteBreadcrumb(Category $category, $isProductPage = false, $parent = false)
    {
        $breadcrumb = [];
        if (! empty($category->parent_id)) {
            $breadcrumb = $this->getSiteBreadcrumb($category->parent, false, true);
        }
        else {
            $breadcrumb[] = (object) [
                "title" => config("category-product.catalogPageName"),
                "url" => route("catalog.categories.index"),
                "active" => false,
            ];
        }

        $breadcrumb[] = (object) [
            "title" => $category->title,
            "url" => route("catalog.categories.show", ["category" => $category]),
            "active" => false,
        ];

        if ($isProductPage) {
            $routeParams = Route::current()->parameters();
            $product = $routeParams["product"];
            $breadcrumb[] = (object) [
                "title" => $product->title,
                "url" => route("catalog.products.show", ["product" => $product]),
                "active" => true,
            ];
        }
        elseif (! $parent) {
            $length = count($breadcrumb);
            $breadcrumb[$length - 1]->active = true;
        }

        return $breadcrumb;
    }

    /**
     * Собрать хлебные крошки для админки.
     *
     * @param Category $category
     * @param bool $isProductPage
     * @return array
     */
    public function getAdminBreadcrumb(Category $category, $isProductPage = false)
    {
        $breadcrumb = [];
        if (! empty($category->parent)) {
            $breadcrumb = $this->getAdminBreadcrumb($category->parent);
        }
        else {
            $breadcrumb[] = (object) [
                "title" => "Категории",
                "url" => route("admin.categories.index"),
                "active" => false,
            ];
        }
        $routeParams = Route::current()->parameters();
        $isProductPage = $isProductPage && ! empty($routeParams["product"]);
        $active = ! empty($routeParams["category"]) &&
                  $routeParams["category"]->id == $category->id &&
                  ! $isProductPage;
        $breadcrumb[] = (object) [
            "title" => $category->title,
            "url" => route("admin.categories.show", ["category" => $category]),
            "active" => $active,
        ];
        if ($isProductPage) {
            $product = $routeParams["product"];
            $breadcrumb[] = (object) [
                "title" => $product->title,
                "url" => route("admin.products.show", ["product" => $product]),
                "active" => true,
            ];
        }
        return $breadcrumb;
    }

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
            event(new CategorySpecificationUpdate($child));
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
        // Изменились характеристики категории.
        event(new CategorySpecificationUpdate($category));
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
            // Обновление Категории.
            $category = Category::query()
                ->where("id", $id)
                ->first();
            $category->priority = $priority;
            $category->parent_id = $parentId;
            $category->save();
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