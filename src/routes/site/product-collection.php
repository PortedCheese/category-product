<?php

use Illuminate\Support\Facades\Route;

Route::group([
    "namespace" => "App\Http\Controllers\Vendor\CategoryProduct\Site",
    "middleware" => ["web"],
    "as" => "catalog.product-collections.",
    "prefix" => "product-collections",
], function () {
    Route::get("/", "ProductCollectionController@index")->name("index");
    Route::get("/{collection}", "ProductCollectionController@show")
        ->name("show");
});
