<?php

namespace PortedCheese\CategoryProduct\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Product;
use App\Specification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PortedCheese\CategoryProduct\Facades\ProductActions;

class ProductSpecificationController extends Controller
{
    /**
     * Список.
     *
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, Product $product)
    {
        $this->authorize("specificationManagement", $product);

        $category = $product->category;
        return view(
            "category-product::admin.product-specifications.index",
            compact("product", "category")
        );
    }

    /**
     * Список текущих характеристик.
     *
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function current(Product $product)
    {
        $this->authorize("specificationManagement", $product);

        return response()
            ->json([
                "items" => ProductActions::getProductSpecifications($product),
                "available" => ProductActions::getAvailableSpecifications($product),
            ]);
    }

    /**
     * Добавление значения.
     *
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request, Product $product)
    {
        $this->authorize("specificationManagement", $product);

        $this->storeValidator($request->all());
        $specId = $request->get("id");

        $product->specifications()->syncWithoutDetaching([
            $specId => [
                "category_id" => $product->category->id,
                "values" => json_encode($request->get("values"))
            ]
        ]);
        return response()
            ->json([
                "success" => true,
                "message" => "Характеристика добавлена",
            ]);
    }

    /**
     * @param $data
     */
    protected function storeValidator($data)
    {
        Validator::make($data, [
            "id" => ["required", "exists:specifications,id"],
            "values" => ["required", "array", "min:1"],
        ], [], [
            "id" => "Характеристика",
            "values" => "Значения",
        ])->validate();
    }

    /**
     * Обновление значений.
     *
     * @param Request $request
     * @param Product $product
     * @param Specification $specification
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, Product $product, Specification $specification)
    {
        $this->authorize("specificationManagement", $product);
        $this->updateValidator($request->all());
        $product->specifications()->updateExistingPivot($specification, [
            "values" => $request->get("values"),
        ]);
        return response()
            ->json([
                "success" => true,
                "message" => "Характеристика обновлена",
            ]);
    }

    /**
     * @param $data
     */
    protected function updateValidator($data)
    {
        Validator::make($data, [
            "values" => ["required", "array", "min:1"],
        ], [], [
            "values" => "Значения",
        ])->validate();
    }

    /**
     * Удалить значение.
     *
     * @param Product $product
     * @param Specification $specification
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Product $product, Specification $specification)
    {
        $this->authorize("specificationManagement", $product);
        $product->specifications()->detach($specification);
        return response()
            ->json([
                "success" => true,
                "message" => "Характеристика успешно удалена",
            ]);
    }
}
