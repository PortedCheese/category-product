<?php

namespace PortedCheese\CategoryProduct\Observers;

use App\Category;
use PortedCheese\BaseSettings\Exceptions\PreventDeleteException;
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
