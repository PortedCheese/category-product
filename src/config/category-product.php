<?php

return [
    // Admin
    "categoryAdminRoutes" => true,
    "categoryShowRouteName" => "admin.categories.show",
    "specificationAdminRoutes" => true,
    "specificationGroupAdminRoutes" => true,
    "productLabelAdminRoutes" => true,
    "productAdminRoutes" => true,
    "productSpecificationAdminRoutes" => true,

    // FACADES
    "categoryFacade" => \PortedCheese\CategoryProduct\Helpers\CategoryActionsManager::class,
    "specificationFacade" => \PortedCheese\CategoryProduct\Helpers\SpecificationActionManager::class,
    "productFacade" => \PortedCheese\CategoryProduct\Helpers\ProductActionsManager::class,
    "productFilterFacade" => \PortedCheese\CategoryProduct\Helpers\ProductFilterManager::class,

    // Site
    "categorySiteRoutes" => true,
    "subCategoriesPage" => false,
];