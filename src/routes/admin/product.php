<?php

use Illuminate\Support\Facades\Route;

Route::group([
    "namespace" => "App\Http\Controllers\Vendor\CategoryProduct\Admin",
    "middleware" => ["web", "management"],
    "as" => "admin.",
    "prefix" => "admin",
], function () {
    // Товары категори.
    Route::resource("categories.products", "ProductController")->shallow();
    // Все товары.
    Route::get("products", "ProductController@index")
        ->name("products.index");
    // Действия с товаром.
    Route::group([
        "prefix" => "products/{product}",
        "as" => "products.",
    ], function () {
        Route::get("meta", "ProductController@meta")
            ->name("meta");
        Route::get("gallery", "ProductController@gallery")
            ->name("gallery");
        Route::put("change-category", "ProductController@changeCategory")
            ->name("change-category");
    });
});