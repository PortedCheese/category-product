<?php


namespace PortedCheese\CategoryProduct\Helpers;


use App\Category;
use App\Product;
use Illuminate\Http\Request;
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

    public function __construct()
    {
        $this->category = null;
        $this->request = null;
        $this->query = Product::query()
            ->whereNotNull("products.published_at");
        $this->categoryIds = [];
    }

    public function filterByCategory(Request $request, Category $category)
    {
        $this->category = $category;
        $this->request = $request;
        $this->initCategoryQuery();
        return $this->makeFilters();
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
        $perPage = config("category-product.categoryProductsPerPage");
        return $this->query->paginate($perPage)->appends($this->request->input());
    }
}