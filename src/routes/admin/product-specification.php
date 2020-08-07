<?php

use Illuminate\Support\Facades\Route;

Route::group([
    "namespace" => "App\Http\Controllers\Vendor\CategoryProduct\Admin",
    "middleware" => ["web", "management"],
    "as" => "admin.products.specifications.",
    "prefix" => "admin/products/{product}/specifications",
], function () {
    Route::get("/", "ProductSpecificationController@index")
        ->name("index");
});

Route::group([
    "namespace" => "App\Http\Controllers\Vendor\CategoryProduct\Admin",
    "middleware" => ["web", "management"],
    "as" => "admin.products.specifications.",
    "prefix" => "admin/ajax/products/{product}/specifications",
], function () {
    Route::get("/", "ProductSpecificationController@current")
        ->name("current");

    Route::post("/", "ProductSpecificationController@store")
        ->name("store");

    Route::put("/{specification}", "ProductSpecificationController@update")
        ->name("update");

    Route::delete("/{specification}", "ProductSpecificationController@destroy")
        ->name("destroy");
});