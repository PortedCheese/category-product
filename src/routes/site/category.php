<?php

use Illuminate\Support\Facades\Route;

Route::group([
    "namespace" => "App\Http\Controllers\Vendor\CategoryProduct\Site",
    "middleware" => ["web"],
    "as" => "catalog.categories.",
    "prefix" => "catalog",
], function () {
    Route::get("/", "CategoryController@index")
        ->name("index");
    Route::get("/{category}", "CategoryController@show")
        ->name("show");
});