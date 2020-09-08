<?php

use Illuminate\Support\Facades\Route;

Route::group([
    "namespace" => "App\Http\Controllers\Vendor\CategoryProduct\Site",
    "middleware" => ["web"],
    "as" => "catalog.products.",
    "prefix" => "products",
], function () {
    Route::get("/{product}", "ProductController@show")
        ->name("show");
});

Route::group([
    "namespace" => "App\Http\Controllers\Vendor\CategoryProduct\Site",
    "middleware" => ["web"],
    "as" => "catalog.labels.",
    "prefix" => "labels",
], function () {
    Route::get("/{label}", "ProductController@label")
        ->name("show");
});