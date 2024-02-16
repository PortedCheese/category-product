<?php

use Illuminate\Support\Facades\Route;

Route::group([
    "namespace" => "App\Http\Controllers\Vendor\CategoryProduct\Admin",
    "middleware" => ["web", "management"],
    "as" => "admin.",
    "prefix" => "admin",
], function () {
    Route::resource("product-collections", "ProductCollectionController")
        ->parameters([
            "product-collections" => "collection",
        ]);

    Route::group([
        'prefix' => 'product-collections/{collection}',
        'as' => 'product-collections.',
    ], function () {
        // Meta.
        Route::get("metas", "ProductCollectionController@metas")
            ->name("metas");
        // Image delete
        Route::delete('delete-image', 'ProductCollectionController@deleteImage')
            ->name('delete-image');
        // Отключение\включение коллекции
        Route::put("published", "ProductCollectionController@changePublished")
            ->name("published");
    });

});