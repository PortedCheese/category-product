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
        return [];
    }

    protected function initCategoryQuery()
    {
        $this->categoryIds = CategoryActions::getCategoryChildren($this->category, true);
        debugbar()->info($this->categoryIds);
    }

    protected function makeFilters()
    {
        foreach ($this->request->all() as $key => $value) {
            if (empty($value)) {
                continue;
            }
        }
    }
}