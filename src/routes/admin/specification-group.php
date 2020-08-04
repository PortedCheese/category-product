<?php

use Illuminate\Support\Facades\Route;

Route::group([
    "namespace" => "App\Http\Controllers\Vendor\CategoryProduct\Admin",
    "middleware" => ["web", "management"],
    "as" => "admin.",
    "prefix" => "admin",
], function () {
    Route::resource("specification-groups", "SpecificationGroupController")
        ->parameters([
            "specification-groups" => "group",
        ])
        ->shallow();
});