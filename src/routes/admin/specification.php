<?php

use Illuminate\Support\Facades\Route;

Route::group([
    "namespace" => "App\Http\Controllers\Vendor\CategoryProduct\Admin",
    "middleware" => ["web", "management"],
    "as" => "admin.",
    "prefix" => "admin",
], function () {
    // Все характеристики.
    Route::get("specifications", "SpecificationController@list")
        ->name("specifications.index");
    // Характеристики внутри категории.
    Route::resource("categories.specifications", "SpecificationController")
        ->except("edit", "destroy")
        ->shallow();
    
    // Редактировать связки.
    Route::group([
        "as" => "categories.specifications.",
        "prefix" => "categories/{category}/specifications/{specification}",
    ], function () {
        Route::get("edit", "SpecificationController@editPivot")
            ->name("edit");
        Route::put("/", "SpecificationController@updatePivot")
            ->name("update");
        Route::delete("/", "SpecificationController@destroyPivot")
            ->name("destroy");
    });
});