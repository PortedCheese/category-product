<?php


namespace PortedCheese\CategoryProduct\Helpers;


use App\Category;
use App\Product;
use PortedCheese\BaseSettings\Exceptions\PreventActionException;
use PortedCheese\CategoryProduct\Facades\CategoryActions;

class ProductActionsManager
{
    /**
     * Изменить категорию товара.
     *
     * @param Product $product
     * @param int $categoryId
     * @throws PreventActionException
     */
    public function changeCategory(Product $product, int $categoryId)
    {
        try {
            $category = Category::query()
                ->where("id", $categoryId)
                ->firstOrFail();
            $original = $product->category;
        }
        catch (\Exception $exception) {
            throw new PreventActionException("Категория не найдена");
        }
        $product->category_id = $categoryId;
        $product->save();
        /**
         * @var Category $category
         * @var Category $original
         */
        $this->changeProductPivots();
        CategoryActions::copyParentSpec($category, $original);
    }

    protected function changeProductPivots()
    {
        // TODO: change field values pivot.
    }
}