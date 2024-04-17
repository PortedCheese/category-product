<?php


namespace PortedCheese\CategoryProduct\Helpers;


use App\Category;
use App\Product;
use App\ProductSpecification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PortedCheese\BaseSettings\Exceptions\PreventActionException;
use PortedCheese\CategoryProduct\Events\CategorySpecificationValuesUpdate;
use PortedCheese\CategoryProduct\Events\ProductListChange;
use PortedCheese\CategoryProduct\Facades\CategoryActions;
use PortedCheese\CategoryProduct\Facades\ProductActions;
use PortedCheese\CategoryProduct\Models\SpecificationGroup;

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
        $class = config("category-product.productSpecificationResource");
        return $class::collection($specifications);
    }

    /**
     * Разбить характеристики по группам.
     *
     * @param Product $product
     * @param Collection|null $collection
     * @return array
     */
    public function getProductSpecificationsByGroups(Product $product, Collection $collection = null)
    {
        if (empty($collection)) $collection = $this->getProductSpecifications($product, true);
        $groups = [];
        $noGroup = [];
        foreach ($collection as $item) {
            /**
             * @var ProductSpecification $item
             */
            $specification = $item->specification;
            $group = $specification->group;
            if (! empty($group)) {
                $groupId = $group->id;
                if (empty($groups[$groupId])) {
                    $groups[$groupId] = [
                        "model" => $group,
                        "title" => $group->title,
                        "specifications" => [],
                    ];
                }
                if (empty($groups[$groupId]["specifications"][$specification->id])) {
                    $groups[$groupId]["specifications"][$specification->id] = (object) [
                        "values" => [],
                        "title" => $item->title,
                    ];
                }
                $groups[$groupId]["specifications"][$specification->id]->values[] = $item->value;
            }
            else {
                if (empty($noGroup[$specification->id])) {
                    $noGroup[$specification->id] = (object) [
                        "values" => [],
                        "title" => $item->title,
                    ];
                }
                $noGroup[$specification->id]->values[] = $item->value;
            }
        }
        $groupsInfo = [];
        // Если есть значения буз группы, их надо в начало.
        if (! empty($noGroup)) {
            $groupsInfo[] = (object) [
                "model" => false,
                "title" => "No group",
                "specifications" => $noGroup,
            ];
        }
        // Определяем порядок групп.
        if (! empty($groups)) {
            $groupIds = array_keys($groups);
            $collectionOfIds = SpecificationGroup::query()
                ->select("id")
                ->whereIn("id", $groupIds)
                ->orderBy("priority")
                ->get();
            foreach ($collectionOfIds as $item) {
                $groupsInfo[] = (object) $groups[$item->id];
            }
        }
        return $groupsInfo;
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
                "type" => $specification->type === "color" ?  $specification->type : "",
            ];
        }
        return $array;
    }

    /**
     * Заполненные значения характеристик для товаров этой категории и дочерних
     *
     * @param Product $product
     * @return array
     */
    public function getAvailableSpecificationsValues(Product $product){
        $category = $product->category;
        list($specValues, $specCodes ) = ProductActions::getProductSpecificationValues($category, true);
        $availableValues=[];
        foreach ($specCodes as $id => $spec){
            foreach ($spec as $value => $code)
            $availableValues[$id][] = ["value" => $value, "code" => $code];
        }
        return $availableValues;
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
        if (! $original->published_at)
            $product->published_at = null;

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
                ->select("specification_id", "value", "code")
                ->whereIn("product_id", $pIds)
                ->orderBy("product_id")
                ->get();
            $specValues = [];
            $specCodes = [];
            foreach ($productValues as $item) {
                $specId = $item->specification_id;
                if (empty($specValues[$specId])) {
                    $specValues[$specId] = [];
                }
                if (! empty($item->value)) {
                    $specValues[$specId] = array_unique(
                        array_merge($specValues[$specId], Arr::wrap($item->value))
                    );
                    $specCodes[$specId][$item->value] = $item->code ?? "";
                }
            }
            return array($specValues, $specCodes);
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
     * Список просмотренных нредавно товаров.
     *
     * @param Product $product
     * @return array|\Illuminate\Database\Eloquent\Builder[]|Collection
     */
    public function getYouWatch(Product $product)
    {
        $watch = $this->getWatchCookie($product);
        if (count($watch) > 5) unset($watch[4]);
        $result = Product::query()
            ->whereIn("id", $watch)
            ->whereNotNull("published_at")
            ->get();
        $items = [];
        foreach ($result as $item) {
            $items[$item->id] = $item;
        }
        $result = [];
        foreach ($watch as $key => $value) {
            if (! empty($items[$value])) $result[] = $items[$value];
        }
        return $result;
    }

    /**
     * Получить список из куки.
     *
     * @param Product $product
     * @return array|mixed|string|null
     */
    protected function getWatchCookie(Product $product)
    {
        $watch = Cookie::get("product-watch");
        $watch = empty($watch) ? [] : json_decode($watch, true);
        $this->clearWatchCurrentProduct($product, $watch);
        $watch = Arr::prepend($watch, $product->id);

        if (count($watch) > 5) unset($watch[4]);

        Cookie::queue("product-watch", json_encode($watch));
        $this->clearWatchCurrentProduct($product, $watch);

        return $watch;
    }

    /**
     * Убрать текущий товар.
     *
     * @param Product $product
     * @param $watch
     */
    protected function clearWatchCurrentProduct(Product $product, &$watch)
    {
        if (in_array($product->id, $watch)) {
            foreach ($watch as $key => $value) {
                if ($value == $product->id) {
                    unset($watch[$key]);
                    break;
                }
            }
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

    /**
     * Получить опубликованные коллекции
     *
     * @param Product $product
     * @return mixed
     */
    public function getProductCollections(Product $product)
    {
        $key = "product-actions-getProductCollections:{$product->id}";
        return Cache::rememberForever($key, function() use ($product) {
            return $product->collections()->select("title", "slug","published_at")->whereNotNull("published_at")->get();
        });
    }

    /**
     * Забыть кэш опубликованных коллекций
     *
     * @param Product $product
     * @return void
     */
    public function forgetProductCollections(Product $product){
        $key = "product-actions-getProductCollections:{$product->id}";
        Cache::forget("$key");
    }

}