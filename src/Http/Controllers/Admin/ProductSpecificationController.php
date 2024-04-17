<?php

namespace PortedCheese\CategoryProduct\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Product;
use App\ProductSpecification;
use App\Specification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PortedCheese\CategoryProduct\Events\CategorySpecificationValuesUpdate;
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
                "availableValues" =>  ProductActions::getAvailableSpecificationsValues($product),
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
        foreach ($request->get("values") as $value) {
            $product->specifications()->create([
                "category_id" => $product->category->id,
                "specification_id" => $specId,
                "value" => $value["value"],
                "code" => $value["code"] ?: null,
            ]);
        }
        // При добавлении характеристики меняется список занчений характеристик.
        $category = $product->category;
        event(new CategorySpecificationValuesUpdate($category));
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
    public function update(Request $request, ProductSpecification $value)
    {
        $product = $value->product;
        $this->authorize("specificationManagement", $product);
        $this->updateValidator($request->all());
        $value->update($request->all());
        // При изменении характеристики меняется список занчений характеристик.
        $category = $product->category;
        event(new CategorySpecificationValuesUpdate($category));
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
            "value" => ["required", "max:250"],
            "code" => ["nullable", "max:250"],
        ], [], [
            "value" => "Значение",
            "code" => "Код",
        ])->validate();
    }

    /**
     * Удалить значение.
     *
     * @param ProductSpecification $value
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(ProductSpecification $value)
    {
        $product = $value->product;
        $this->authorize("specificationManagement", $product);
        $value->delete();
        // При удалении характеристики меняется список занчений характеристик.
        $category = $product->category;
        event(new CategorySpecificationValuesUpdate($category));
        return response()
            ->json([
                "success" => true,
                "message" => "Характеристика успешно удалена",
            ]);
    }
}
