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
    "as" => "catalog.favorite.",
    "prefix" => "favorite",
], function () {
    Route::get("/", "ProductController@favoriteList")
        ->name("index");
    Route::post("/{product}", "ProductController@addToFavorite")
        ->name("add-to-favorite");
    Route::delete("/{product}", "ProductController@removeFromFavorite")
        ->name("remove-from-favorite");
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