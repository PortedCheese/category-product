<?php

use Illuminate\Support\Facades\Route;

Route::group([
    "namespace" => "App\Http\Controllers\Vendor\CategoryProduct\Admin",
    "middleware" => ["web", "management"],
    "as" => "admin.",
    "prefix" => "admin",
], function () {
    Route::resource("categories", "CategoryController");
    
    // Изменить вес у категорий.
    Route::put("categories/tree/priority", "CategoryController@changeItemsPriority")
        ->name("categories.item-priority");

    Route::group([
        "prefix" => "categories/{category}",
        "as" => "categories.",
    ], function () {
        // Добавить подкатегорию.
        Route::get("create-child", "CategoryController@create")
            ->name("create-child");
        // Сохранить подкатегорию.
        Route::post("store-child", "CategoryController@store")
            ->name("store-child");
        // Meta.
        Route::get("metas", "CategoryController@metas")
            ->name("metas");
        // Отключение\включение категории.
        Route::put("published", "CategoryController@changePublished")
            ->name("published");
    });
});