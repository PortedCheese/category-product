<?php

namespace PortedCheese\CategoryProduct\Observers;

use App\Category;
use PortedCheese\BaseSettings\Exceptions\PreventDeleteException;
use PortedCheese\CategoryProduct\Events\CategoryChangePosition;
use PortedCheese\CategoryProduct\Facades\CategoryActions;

class CategoryObserver
{
    /**
     * Перед сохранением.
     *
     * @param Category $category
     */
    public function creating(Category $category) {
        $max = Category::query()
            ->where("parent_id", $category->parent_id)
            ->max("priority");
        $category->priority = $max + 1;
    }

    /**
     * После создания.
     *
     * @param Category $category
     */
    public function created(Category $category)
    {
        // Скопировать поля родителя.
        CategoryActions::copyParentSpec($category);

        event(new CategoryChangePosition($category));
    }

    /**
     * После обновления.
     *
     * @param Category $category
     */
    public function updating(Category $category)
    {
        $original = $category->getOriginal();
        if ($original["parent_id"] != $category->parent_id) {
            $this->categoryChangedParent($category, $original["parent_id"]);
        }
    }

    /**
     * Очистить список id дочерних категорий.
     *
     * @param Category $category
     * @param $parent
     */
    protected function categoryChangedParent(Category $category, $parent)
    {
        if (! empty($parent)) {
            $parent = Category::find($parent);
            event(new CategoryChangePosition($parent));
        }
        event(new CategoryChangePosition($category));
    }

    /**
     * Перед удалением.
     *
     * @param Category $category
     * @throws PreventDeleteException
     */
    public function deleting(Category $category)
    {
        if ($category->children->count()) {
            throw new PreventDeleteException("Невозможно удалить категорию, у нее есть подкатегории");
        }

        if ($category->products->count()) {
            throw new PreventDeleteException("Невозможно удалить категорию, у нее есть товары");
        }
    }
}
