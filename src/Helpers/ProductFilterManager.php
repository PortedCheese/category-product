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
    protected $ranges;

    public function __construct()
    {
        $this->category = null;
        $this->request = null;
        $this->ranges = [];
        $this->query = null;
        $this->categoryIds = [];
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
        $this->query = Product::query()
            ->whereNotNull("products.published_at");
    }

    /**
     * Добавить категории к запросу.
     */
    protected function initCategoryQuery()
    {
        $this->categoryIds = CategoryActions::getCategoryChildren($this->category, true);
        $this->query->whereIn("products.category_id", $this->categoryIds);
    }

    /**
     * Фильтрация.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function makeFilters()
    {
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
        $defaultSortDirection = config("category-product.defaultSortDirection");
        if ($sort = $this->request->get("sort-by", false)) {
            $direction = $this->request->get("sort-order", $defaultSortDirection);
            if (! in_array($direction, ["asc", "desc"])) $direction = $defaultSortDirection;

            if (Schema::hasColumn("products", $sort)) {
                $this->query->orderBy("products.{$sort}", $direction);
                $defaultSort = false;
            }
        }
        if ($defaultSort) {
            $this->query->orderBy(
                "products." . config("category-product.defaultSort"),
                $defaultSortDirection
            );
        }
    }
}