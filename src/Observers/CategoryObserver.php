<?php

namespace PortedCheese\CategoryProduct\Observers;

use App\Category;
use PortedCheese\BaseSettings\Exceptions\PreventDeleteException;

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
