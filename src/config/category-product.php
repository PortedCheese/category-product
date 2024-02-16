<?php

return [
    // Admin
    "categoryAdminRoutes" => true,
    "categoryShowRouteName" => "admin.categories.show",
    "specificationAdminRoutes" => true,
    "specificationGroupAdminRoutes" => true,
    "productLabelAdminRoutes" => true,
    "productCollectionAdminRoutes" => true,
    "productAdminRoutes" => true,
    "productSpecificationAdminRoutes" => true,

    // FACADES
    "categoryFacade" => \PortedCheese\CategoryProduct\Helpers\CategoryActionsManager::class,
    "specificationFacade" => \PortedCheese\CategoryProduct\Helpers\SpecificationActionManager::class,
    "productFacade" => \PortedCheese\CategoryProduct\Helpers\ProductActionsManager::class,
    "productFilterFacade" => \PortedCheese\CategoryProduct\Helpers\ProductFilterManager::class,
    "productFavoriteFacade" => \PortedCheese\CategoryProduct\Helpers\ProductFavoriteManager::class,

    // Resources.
    "productSpecificationResource" => \PortedCheese\CategoryProduct\Http\Resources\ProductSpecification::class,

    // Site
    "categorySiteRoutes" => true,
    "subCategoriesPage" => false,
    "categoryProductsPerPage" => 18,
    "productSiteRoutes" => true,
    // @var ["list", "bar"]
    "defaultProductView" => "bar",
    "useSimpleTeaser" => true,
    "catalogPageName" => "Каталог",
    "categoryNest" => 3,

    // Sort
    "defaultSort" => "title",
    "defaultSortDirection" => "asc",
    "defaultCheckboxLimit" => 3,
    "sortOptions" => [
        "title.asc" => (object) [
            "title" => "По названию",
            "by" => "title",
            "direction" => "asc",
            "ico" => "catalog-sort-alpha",
            "arrow" => "down",
        ],
        "title.desc" => (object) [
            "title" => "По названию",
            "by" => "title",
            "direction" => "desc",
            "ico" => "catalog-sort-alpha",
            "arrow" => "up",
        ],
        "published_at.desc" => (object) [
            "title" => "Сначала новые",
            "by" => "published_at",
            "direction" => "desc",
            "ico" => "catalog-sort-amount",
            "arrow" => "down",
        ],
        "published_at.asc" => (object) [
            "title" => "Сначала старые",
            "by" => "published_at",
            "direction" => "asc",
            "ico" => "catalog-sort-amount",
            "arrow" => "up",
        ],
    ],
];