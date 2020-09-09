<?php


namespace PortedCheese\CategoryProduct\Helpers;


use App\Category;
use App\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PortedCheese\BaseSettings\Exceptions\PreventActionException;
use PortedCheese\CategoryProduct\Events\CategorySpecificationValuesUpdate;
use PortedCheese\CategoryProduct\Events\ProductListChange;
use PortedCheese\CategoryProduct\Facades\CategoryActions;
use PortedCheese\CategoryProduct\Http\Resources\ProductSpecification as ValueResource;

class ProductActionsManager
{
    /**
     * Характеристики товара.
     *
     * @param Product $product
     * @param bool $collection
     * @return Collection|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getProductSpecifications(Product $product, bool $collection = false)
    {
        $specifications = $product->specifications()
            ->join("category_specification", function(JoinClause $join) {
                $join->on("product_specifications.specification_id", "=", "category_specification.specification_id")
                    ->on("product_specifications.category_id", "=", "category_specification.category_id");
            })
            ->with("specification", "category", "specification.group")
            ->orderBy("category_specification.priority")
            ->orderBy("value")
            ->get();
        if ($collection) {
            return $specifications;
        }
        return ValueResource::collection($specifications);
    }

    /**
     * Доступные характеристики.
     *
     * @param Product $product
     * @param bool $collection
     * @return array|Collection
     */
    public function getAvailableSpecifications(Product $product, bool $collection = false)
    {
        $category = $product->category()
            ->with(["specifications" => function (BelongsToMany $query) {
                $query->orderBy("priority");
            }])
            ->first();
        /**
         * @var Category $category
         */
        $specifications = $category->specifications;
        /**
         * @var Collection $specifications
         */
        if ($collection) {
            return $specifications;
        }
        $array = [];
        foreach ($specifications as $specification) {
            $pivot = $specification->pivot;
            $array[] = [
                "id" => $specification->id,
                "title" => $pivot->title,
                "filter" => $pivot->filter,
            ];
        }
        return $array;
    }

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
        $this->changeProductPivots($original->id, $categoryId, $product->id);
        CategoryActions::copyParentSpec($category, $original);
        // При переносе товара в другую категорию у двух категорий меняется набор значений характеристик и товаров.
        event(new CategorySpecificationValuesUpdate($category));
        event(new ProductListChange($category));
        event(new CategorySpecificationValuesUpdate($original));
        event(new ProductListChange($original));
    }

    /**
     * Получить значения характеристик товаров.
     *
     * @param Category $category
     * @param bool $includeSubs
     * @return array
     */
    public function getProductSpecificationValues(Category $category, $includeSubs = false)
    {
        $key = "product-actions-getProductSpecificationValues:{$category->id}";
        $key .= $includeSubs ? "-true" : "-false";
        return Cache::rememberForever($key, function() use ($category, $includeSubs) {
            $pIds = $this->getCategoryProductIds($category, $includeSubs);
            // Найти значения товаров.
            $productValues = DB::table("product_specifications")
                ->select("specification_id", "value")
                ->whereIn("product_id", $pIds)
                ->orderBy("product_id")
                ->get();
            $specValues = [];
            foreach ($productValues as $item) {
                $specId = $item->specification_id;
                if (empty($specValues[$specId])) {
                    $specValues[$specId] = [];
                }
                if (! empty($item->value)) {
                    $specValues[$specId] = array_unique(
                        array_merge($specValues[$specId], Arr::wrap($item->value))
                    );
                }
            }
            return $specValues;
        });
    }

    /**
     * Очистить кэш значений карактеристик товаров.
     *
     * @param Category $category
     */
    public function forgetProductSpecificationsValues(Category $category)
    {
        $key = "product-actions-getProductSpecificationValues:{$category->id}";
        Cache::forget("$key-true");
        Cache::forget("$key-false");
        if (! empty($category->parent_id)) {
            $this->forgetProductSpecificationsValues($category->parent);
        }
    }

    /**
     * Получить id товаров категории, либо категории и подкатегорий.
     *
     * @param Category $category
     * @param $includeSubs
     * @return mixed
     */
    public function getCategoryProductIds(Category $category, $includeSubs = false)
    {
        $key = "product-actions-getCategoryProductIds:{$category->id}";
        $key .= $includeSubs ? "-true" : "-false";
        return Cache::rememberForever($key, function() use ($category, $includeSubs) {
            $query = Product::query()
                ->select("id")
                ->whereNotNull("published_at");
            if ($includeSubs) {
                $query->whereIn("category_id", CategoryActions::getCategoryChildren($category, true));
            }
            else {
                $query->where("category_id", $category->id);
            }
            $products = $query->get();
            $pIds = [];
            foreach ($products as $product) {
                $pIds[] = $product->id;
            }
            return $pIds;
        });
    }

    /**
     * Очистить кэш идентификаторов товаров.
     *
     * @param Category $category
     */
    public function forgetCategoryProductIds(Category $category)
    {
        $key = "product-actions-getCategoryProductIds:{$category->id}";
        Cache::forget("$key-true");
        Cache::forget("$key-false");
        if (! empty($category->parent_id)) {
            $this->forgetCategoryProductIds($category->parent);
        }
    }

    /**
     * Изменить категорию у таблицы связки.
     *
     * @param $originalId
     * @param $newId
     * @param $productId
     */
    protected function changeProductPivots($originalId, $newId, $productId)
    {
        DB::table("product_specifications")
            ->where("category_id", $originalId)
            ->where("product_id", $productId)
            ->update([
                "category_id" => $newId
            ]);
    }
}