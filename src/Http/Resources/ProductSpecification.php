<?php

namespace PortedCheese\CategoryProduct\Http\Resources;

use App\Specification;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSpecification extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $model = $this->resource;
        /**
         * @var \App\ProductSpecification $model
         */
        $array = parent::toArray($request);
        $array["spec_type_title"] = Specification::getTypeByKey($model->specification->type);
        $array["group_title"] = $model->specification->group ? $model->specification->group->title : false;
        $array["deleteUrl"] = route("admin.products.specifications.destroy", ["value" => $model]);
        $array["updateUrl"] = route("admin.products.specifications.update", ["value" => $model]);
        return $array;
    }
}
