<?php


namespace PortedCheese\CategoryProduct\Helpers;


use App\Category;
use App\Product;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use PortedCheese\CategoryProduct\Facades\CategoryActions;

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