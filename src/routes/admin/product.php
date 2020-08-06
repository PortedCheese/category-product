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
        // Метатеги.
        Route::get("meta", "ProductController@meta")
            ->name("meta");
        // Галерея.
        Route::get("gallery", "ProductController@gallery")
            ->name("gallery");
        // Изменение категории.
        Route::put("change-category", "ProductController@changeCategory")
            ->name("change-category");
        // Отключение\включение товара.
        Route::put("published", "ProductController@changePublished")
            ->name("published");
    });
});