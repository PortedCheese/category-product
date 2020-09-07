<?php


namespace PortedCheese\CategoryProduct\Helpers;


use App\Category;
use App\Product;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use PortedCheese\CategoryProduct\Facades\CategoryActions;
use PortedCheese\CategoryProduct\Facades\ProductActions;
use PortedCheese\CategoryProduct\Facades\SpecificationActions;
use PortedCheese\ProductVariation\Facades\ProductVariationActions;

class ProductFilterManager
{
    /**
     * @var null|Category
     */
    protected $category;
    /**
     * @var null|Request
     */
    protected $request;
    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $query;
    /**
     * @var array
     */
    protected $categoryIds;

    protected $slugValues;
    protected $ranges;

    public function __construct()
    {
        $this->category = null;
        $this->request = null;
        $this->ranges = [];
        $this->query = null;
        $this->categoryIds = [];
        $this->slugValues = [];
    }

    /**
     * Получить данные для фильтра.
     *
     * @param Category $category
     * @param bool $includeSubs
     * @return array
     */
    public function getFilters(Category $category, $includeSubs = false)
    {
        $specInfo = $includeSubs ?
            SpecificationActions::getCategoryChildrenSpecificationsInfo($category) :
            SpecificationActions::getCategorySpecificationsInfo($category, true);

        $specValues = ProductActions::getProductSpecificationValues($category, true);
        // Обход полученных значений и распределение по полям.
        $this->setProductValuesToFilters($specInfo, $specValues);
        $this->setPriceFilter($category, $specInfo, $includeSubs);
        $this->prepareRangeFilters($specInfo);
        $this->prepareCheckboxFilters($specInfo);
        return $specInfo;
    }

    /**
     * Возможные сортировки и текущая.
     *
     * @return array
     */
    public function getSortOptions()
    {
        $options = config("category-product.sortOptions");
        $request = app(Request::class);
        /**
         * @var Request $request
         */
        $field = $request->get("sort-by", config("category-product.defaultSort"));
        $order = $request->get("sort-order", config("category-product.defaultSortDirection"));
        return [$options, $field, $order];
    }

    /**
     * Данные для сортировки.
     *
     * @return array
     */
    public function getSortLinkData()
    {
        $request = app(Request::class);
        /**
         * @var Request $request
         */
        $field = $request->get("sort-by", config("category-product.defaultSort"));
        $order = $request->get("sort-order", config("category-product.defaultSortDirection"));
        $queryParams = $request->all();
        if (! empty($queryParams["sort-by"])) unset($queryParams["sort-by"]);
        if (! empty($queryParams["sort-order"])) unset($queryParams["sort-order"]);
        $route = Route::current();
        $routeName = Route::currentRouteName();
        $routeParams = $route->parameters();
        foreach ($queryParams as $key => $value) {
            $routeParams[$key] = $value;
        }
        $uri = route($routeName, $routeParams);
        return [$field, $order, $uri, empty($queryParams)];
    }

    /**
     * Фильтрация по категории.
     *
     * @param Request $request
     * @param Category $category
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function filterByCategory(Request $request, Category $category)
    {
        $this->category = $category;
        $this->request = $request;
        $this->initQuery();
        $this->initCategoryQuery();
        return $this->makeFilters();
    }

    /**
     * Фильтр по цене.
     *
     * @param Category $category
     * @param array $specInfo
     * @param bool $includeSubs
     */
    protected function setPriceFilter(Category $category, array &$specInfo, bool $includeSubs)
    {
        if (! config("product-variation.enablePriceFilter")) return;
        $specInfo = ProductVariationActions::addPriceFilter($category, $specInfo, $includeSubs);
    }

    /**
     * Добавить переменные в чекбоксы.
     *
     * @param $specInfo
     */
    protected function prepareCheckboxFilters(&$specInfo)
    {
        $request = app(Request::class);
        foreach ($specInfo as $key => &$filter) {
            if ($filter->type !== "checkbox") continue;
            $current = $request->get("check-{$filter->slug}", []);
            $vueValues = [];
            $i = 0;
            foreach ($filter->values as $id => $value) {
                $i++;
                $vueValues[] = [
                    "id" => $id,
                    "value" => $value,
                    "checked" => in_array($value, $current),
                    "inputName" => "check-{$filter->slug}[]",
                    "inputId" => "{$id}-{$filter->slug}-{$i}",
                ];
            }
            $filter->vueValues = $vueValues;
        }
    }

    /**
     * Дбавить переменные в диапазоны.
     *
     * @param $specInfo
     */
    protected function prepareRangeFilters(&$specInfo)
    {
        foreach ($specInfo as $key => &$filter) {
            if ($filter->type !== "range") continue;
            $filter->render = $this->checkCanRangeRender($filter);
            if ($filter->render) {
                $filter->min = min($filter->values);
                $filter->max = max($filter->values);
            }
            else {
                unset($specInfo[$key]);
            }
        }
    }

    /**
     * Проверит можно ли вывести диапазон.
     *
     * @param $filter
     * @return bool
     */
    protected function checkCanRangeRender($filter)
    {
        if (empty($filter->values)) return false;
        if (count($filter->values) <= 1) return false;
        if (! empty($filter->values)) {
            foreach ($filter->values as $value) {
                if (! is_numeric($value)) return false;
            }
        }
        return true;
    }

    /**
     * Добавить значения для фильтров и убрать пустые.
     *
     * @param $specInfo
     * @param $specValues
     */
    protected function setProductValuesToFilters(&$specInfo, $specValues)
    {
        // Записать значения для характеристик.
        foreach ($specInfo as &$spec) {
            if (! isset($spec->values)) {
                $spec->values = [];
            }
            $specId = $spec->id;
            if (empty($specValues[$specId])) {
                continue;
            }
            $spec->values = Arr::sort($specValues[$specId]);
        }

        // Убрать пустые.
        foreach ($specInfo as $key => $item) {
            if (empty($item->values)) {
                unset($specInfo[$key]);
            }
        }
    }

    /**
     * Инициализация запроса.
     */
    protected function initQuery()
    {
        $this->query = Product::query();
        // Если есть пакет с ценами и включена сортировка по цене добавить поле по которому сортировать.
        if (config("product-variation.enablePriceSort")) {
            if ($this->getCurrentSortDirection() == "desc") {
                $value = -1;
            }
            else {
                $value = config("product-variation.priceSortReplaceNull");
            }
            $this->query->select("*", DB::raw("if (`minimal` is not null, `minimal`, $value) `priceSort`"));
        }
        else {
            $this->query->select("*");
        }
        $this->query->whereNotNull("products.published_at");
    }

    /**
     * Добавить категории к запросу.
     */
    protected function initCategoryQuery()
    {
        $this->categoryIds = CategoryActions::getCategoryChildren($this->category, true);
        $this->query->whereIn("products.category_id", $this->categoryIds);

        $specInfo = SpecificationActions::getCategoryChildrenSpecificationsInfo($this->category);
        foreach ($specInfo as $item) {
            $this->slugValues[$item->slug] = [
                "id" => $item->id,
                "type" => $item->type,
            ];
        }
        if (config("product-variation.enablePriceFilter")) {
            $this->slugValues[config("product-variation.priceFilterKey")] = [
                "id" => 0,
                "type" => "range",
            ];
        }
    }

    /**
     * Фильтрация.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function makeFilters()
    {
        foreach ($this->request->all() as $key => $value) {
            if (empty($value)) continue;
            if ($this->addSelectToQuery($key, $value)) continue;
            if ($this->addCheckboxToQuery($key, $value)) continue;
            if ($this->prepareRangesForQuery($key, $value)) continue;
        }
        $this->addRangesToQuery();
        $this->query->groupBy("products.id");
        $this->addSortCondition();
        $perPage = config("category-product.categoryProductsPerPage");
        return $this->query->paginate($perPage)->appends($this->request->input());
    }

    /**
     * Сортировка товаров.
     */
    protected function addSortCondition()
    {
        $defaultSort = true;
        if ($sort = $this->request->get("sort-by", false)) {
            $direction = $this->getCurrentSortDirection();

            if (Schema::hasColumn("products", $sort)) {
                $this->query->orderBy("products.{$sort}", $direction);
                $defaultSort = false;
            }
            elseif ($sort == "price" && config("product-variation.enablePriceSort")) {
                $this->query->orderBy("priceSort", $direction);
                $defaultSort = false;
            }
        }
        if ($defaultSort) {
            $this->query->orderBy(
                "products." . config("category-product.defaultSort"),
                $this->getCurrentSortDirection()
            );
        }
        // Сортирует с помощью стандартной сортировки элементы с одинаковым значением.
        $this->query->orderBy(
            "products." . config("category-product.defaultSort"),
            config("category-product.defaultSortDirection")
        );
    }

    /**
     * Текущее направление сортировки.
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    protected function getCurrentSortDirection()
    {
        $defaultSortDirection = config("category-product.defaultSortDirection");
        $direction = $this->request->get("sort-order", $defaultSortDirection);
        if (! in_array($direction, ["asc", "desc"])) $direction = $defaultSortDirection;
        return $direction;
    }

    /**
     * Добавить селект в запрос.
     *
     * @param $key
     * @param $value
     * @return bool
     */
    protected function addSelectToQuery($key, $value)
    {
        if (strstr($key, "select-") === false) return false;

        $slug = str_replace("select-", "", $key);
        if (empty($this->slugValues[$slug])) return true;

        $selects = DB::table("product_specifications")
            ->select("product_id")
            ->where("specification_id", $this->slugValues[$slug]["id"])
            ->where("value", $value)
            ->groupBy("product_id");

        $this->query->joinSub($selects, $slug, function (JoinClause $join) use ($slug) {
            $join->on("products.id", "=", "{$slug}.product_id");
        });

        return true;
    }

    /**
     * Добавить чекбоксы к запросу.
     *
     * @param $key
     * @param $value
     * @return bool
     */
    protected function addCheckboxToQuery($key, $value)
    {
        if (strstr($key, "check-") === false) return false;

        $slug = str_replace("check-", "", $key);
        if (empty($this->slugValues[$slug])) return true;

        $checkboxes = DB::table("product_specifications")
            ->select("product_id")
            ->where("specification_id", $this->slugValues[$slug]["id"])
            ->whereIn("value", $value)
            ->groupBy("product_id");

        $this->query->joinSub($checkboxes, $slug, function (JoinClause $join) use ($slug) {
            $join->on("products.id", "=", "{$slug}.product_id");
        });

        return true;
    }

    /**
     * Подготовить диапазоны.
     *
     * @param $key
     * @param $value
     * @return bool
     */
    protected function prepareRangesForQuery($key, $value)
    {
        if (strstr($key, "range-") === false) return false;

        $sub = str_replace("range-", "", $key);
        if (strstr($sub, "from-") !== false) {
            $operator = "from";
            $slug = str_replace("from-", "", $sub);
        }
        elseif (strstr($sub, "to-") !== false) {
            $operator = "to";
            $slug = str_replace("to-", "", $sub);
        }
        if (empty($this->slugValues[$slug])) return false;
        if (empty($this->ranges[$slug])) {
            $this->ranges[$slug] = [
                "from" => false,
                "to" => false,
            ];
        }
        $this->ranges[$slug][$operator] = (int) $value;

        return true;
    }

    /**
     * Добавить диапазоны к запросу.
     */
    protected function addRangesToQuery()
    {
        foreach ($this->ranges as $slug => $range) {
            if (
                config("product-variation.enablePriceFilter") &&
                $slug == config("product-variation.priceFilterKey")
            ) {
                $ranges = ProductVariationActions::getPriceQuery($range);

                $this->query->leftJoinSub($ranges, $slug, function (JoinClause $join) use ($slug) {
                    $join->on("products.id", "=", "{$slug}.product_id");
                });
            }
            else {
                $ranges = DB::table("product_specifications")
                    ->select("product_id")
                    ->where("specification_id", $this->slugValues[$slug]["id"])
                    ->whereBetween("value", [$range["from"], $range["to"]])
                    ->groupBy("product_id");

                $this->query->joinSub($ranges, $slug, function (JoinClause $join) use ($slug) {
                    $join->on("products.id", "=", "{$slug}.product_id");
                });
            }
        }

        if (config("product-variation.enablePriceSort") && empty($this->ranges[config("product-variation.priceFilterKey")])) {
            $range = ["from" => 0, "to" => 0];
            $ranges = ProductVariationActions::getPriceQuery($range, false);
            $key = config("product-variation.priceFilterKey");
            $this->query->leftJoinSub($ranges, $key, function(JoinClause $join) use ($key) {
                $join->on("products.id", "=", "{$key}.product_id");
            });
        }
    }
}