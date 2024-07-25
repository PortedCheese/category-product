<?php

namespace PortedCheese\CategoryProduct;

use App\Category;
use App\Observers\Vendor\CategoryProduct\CategoryObserver;
use App\Observers\Vendor\CategoryProduct\ProductLabelObserver;
use App\Observers\Vendor\CategoryProduct\ProductCollectionObserver;
use App\Observers\Vendor\CategoryProduct\ProductObserver;
use App\Observers\Vendor\CategoryProduct\SpecificationGroupObserver;
use App\Observers\Vendor\CategoryProduct\ProductSpecificationObserver;
use App\Product;
use App\ProductCollection;
use App\ProductLabel;
use App\ProductSpecification;
use App\SpecificationGroup;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use PortedCheese\BaseSettings\Events\ImageUpdate;
use PortedCheese\CategoryProduct\Console\Commands\CategoryProductMakeCommand;
use PortedCheese\CategoryProduct\Events\CategoriesAddonsUpdate;
use PortedCheese\CategoryProduct\Events\CategoryChangePosition;
use PortedCheese\CategoryProduct\Events\CategorySpecificationUpdate;
use PortedCheese\CategoryProduct\Events\CategorySpecificationValuesUpdate;
use PortedCheese\CategoryProduct\Facades\CategoryActions;
use PortedCheese\CategoryProduct\Facades\ProductFavorite;
use PortedCheese\CategoryProduct\Facades\ProductFilters;
use PortedCheese\CategoryProduct\Filters\CatalogTeaserLg;
use PortedCheese\CategoryProduct\Filters\CatalogTeaserMd;
use PortedCheese\CategoryProduct\Filters\CatalogTeaserSm;
use PortedCheese\CategoryProduct\Filters\CatalogTeaserXl;
use PortedCheese\CategoryProduct\Filters\CatalogTeaserXs;
use PortedCheese\CategoryProduct\Filters\CatalogSimpleTeaserLg;
use PortedCheese\CategoryProduct\Filters\CatalogSimpleTeaserMd;
use PortedCheese\CategoryProduct\Filters\CatalogSimpleTeaserSm;
use PortedCheese\CategoryProduct\Filters\CatalogSimpleTeaserXl;
use PortedCheese\CategoryProduct\Filters\CatalogSimpleTeaserXs;
use PortedCheese\CategoryProduct\Filters\ProductShowLg;
use PortedCheese\CategoryProduct\Filters\ProductShowMd;
use PortedCheese\CategoryProduct\Filters\ProductShowSm;
use PortedCheese\CategoryProduct\Filters\ProductShowXl;
use PortedCheese\CategoryProduct\Filters\ProductSimpleShowLg;
use PortedCheese\CategoryProduct\Filters\ProductSimpleShowMd;
use PortedCheese\CategoryProduct\Filters\ProductSimpleShowSm;
use PortedCheese\CategoryProduct\Filters\ProductSimpleShowXl;
use PortedCheese\CategoryProduct\Filters\ProductSimpleTeaserLg;
use PortedCheese\CategoryProduct\Filters\ProductTeaserLg;
use PortedCheese\CategoryProduct\Filters\ProductThumbShow;
use PortedCheese\CategoryProduct\Filters\ProductThumbSimpleShow;
use PortedCheese\CategoryProduct\Listeners\CategoriesAddonsClearCache;
use PortedCheese\CategoryProduct\Listeners\CategoryIdsInfoClearCache;
use PortedCheese\CategoryProduct\Listeners\CategorySpecificationClearCache;
use PortedCheese\CategoryProduct\Listeners\CategorySpecificationValuesClearCache;
use PortedCheese\CategoryProduct\Listeners\ProductGalleryChange;


class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        // Публикация конфигурации.
        $this->publishes([
          __DIR__ . '/config/category-product.php' => config_path('category-product.php'),
        ], 'config');

        // Расширить конфиг сайта.
        $this->extendConfigVariables();

        // Подключение миграций.
        $this->loadMigrationsFrom(__DIR__ . "/database/migrations");

        // Console.
        if ($this->app->runningInConsole()) {
          $this->commands([
              CategoryProductMakeCommand::class,
          ]);
        }

        // Подключение путей.
        $this->addRoutes();

        // Подключение шаблонов.
        $this->loadViewsFrom(__DIR__ . "/resources/views", "category-product");

        // Assets.
        $this->publishes([
            __DIR__ . '/resources/js/components' => resource_path('js/components/vendor/category-product'),
            __DIR__ . '/resources/js/scripts' => resource_path('js/vendor/category-product'),
            __DIR__ . "/resources/sass" => resource_path("sass/vendor/category-product")
        ], 'public');

        // Наблюдатели.
        $this->addObservers();

        // События.
        $this->makeEvents();

        // Расширить blade.
        $this->expandBlade();
    }

    public function register()
    {
        // Конфигурация стандартная.
        $this->mergeConfigFrom(
          __DIR__ . '/config/category-product.php', 'category-product'
        );
        // Facades.
        $this->initFacades();
    }

    /**
     * Add Blade includes adn variables.
     */
    protected function expandBlade()
    {
        view()->composer("category-product::site.includes.sort-link", function (View $view) {
            list($field, $order, $uri, $params) = ProductFilters::getSortLinkData();
            $view->with("sortField", $field);
            $view->with("sortOrder", $order);
            $view->with("sortUrl", $uri);
            $view->with("noParams", $params);
        });
        Blade::include("category-product::site.includes.sort-text", "sortText");
        Blade::include("category-product::site.includes.sort-link", "sortLink");

        view()->composer(["category-product::site.products.includes.grid-sort", "category-product::site.filters.form"], function (View $view) {
            list($options, $field, $order) = ProductFilters::getSortOptions();
            $view->with("sortOptions", $options);
            $view->with("sortBy", $field);
            $view->with("sortDirection", $order);
        });

        view()->composer(["category-product::site.products.includes.favorite", "category-product::site.includes.favorite-state"], function (View $view) {
            $view->with("items", ProductFavorite::getCurrentFavorite());
        });

        view()->composer("category-product::site.includes.categories-menu", function (View $view) {
            $view->with("categoriesTree", CategoryActions::getTree(true));
        });
    }

    /**
     * Подписка на события.
     */
    protected function makeEvents()
    {
        // Обновление галереи.
        $this->app["events"]->listen(ImageUpdate::class, ProductGalleryChange::class);
        // Обновление полей.
        $this->app["events"]->listen(CategorySpecificationUpdate::class, CategorySpecificationClearCache::class);
        // Изменение позиции категории.
        $this->app["events"]->listen(CategoryChangePosition::class, CategoryIdsInfoClearCache::class);
        // Изменение значений характеристик.
        $this->app["events"]->listen(CategorySpecificationValuesUpdate::class, CategorySpecificationValuesClearCache::class);
        // Изменение дополнений к товарам в дочерних категориях
        $this->app["events"]->listen(CategoriesAddonsUpdate::class, CategoriesAddonsClearCache::class);
    }

    /**
     * Добавление наблюдателей.
     */
    protected function addObservers()
    {
        if (class_exists(ProductObserver::class) && class_exists(Product::class)) {
            Product::observe(ProductObserver::class);
        }
        if (class_exists(CategoryObserver::class) && class_exists(Category::class)) {
            Category::observe(CategoryObserver::class);
        }
        if (class_exists(ProductLabelObserver::class) && class_exists(ProductLabel::class)) {
            ProductLabel::observe(ProductLabelObserver::class);
        }
        if (class_exists(ProductCollectionObserver::class) && class_exists(ProductCollection::class)) {
            ProductCollection::observe(ProductCollectionObserver::class);
        }
        if (class_exists(SpecificationGroupObserver::class) && class_exists(SpecificationGroup::class)) {
            SpecificationGroup::observe(SpecificationGroupObserver::class);
        }
        if (class_exists(ProductSpecificationObserver::class) && class_exists(ProductSpecification::class)) {
            ProductSpecification::observe(ProductSpecificationObserver::class);
        }
    }

    /**
     * Подключение Facades.
     */
    protected function initFacades()
    {
        $this->app->singleton("category-actions", function () {
            $class = config("category-product.categoryFacade");
            return new $class;
        });

        $this->app->singleton("specification-actions", function () {
            $class = config("category-product.specificationFacade");
            return new $class;
        });

        $this->app->singleton("product-actions", function () {
            $class = config("category-product.productFacade");
            return new $class;
        });

        $this->app->singleton("product-filters", function () {
            $class = config("category-product.productFilterFacade");
            return new $class;
        });

        $this->app->singleton("product-favorite", function() {
            $class = config("category-product.productFavoriteFacade");
            return new $class;
        });
    }

    /**
     * Подключение путей.
     */
    protected function addRoutes()
    {
        $this->addAdminRoutes();
        $this->addSiteRoutes();
    }

    /**
     * Пути для сайта.
     */
    protected function addSiteRoutes()
    {
        // Категории.
        if (config("category-product.categorySiteRoutes")) {
            $this->loadRoutesFrom(__DIR__ . "/routes/site/category.php");
        }
        // Товары.
        if (config("category-product.productSiteRoutes")) {
            $this->loadRoutesFrom(__DIR__ . "/routes/site/product.php");
        }
        // Коллекциии товаров.
        if (config("category-product.productCollectionSiteRoutes")) {
            $this->loadRoutesFrom(__DIR__ . "/routes/site/product-collection.php");
        }
    }

    /**
     * Пути для админки.
     */
    protected function addAdminRoutes()
    {
        // Управление категориями.
        if (config("category-product.categoryAdminRoutes")) {
            $this->loadRoutesFrom(__DIR__ . "/routes/admin/category.php");
        }
        // Управление характеристиками.
        if (config("category-product.specificationAdminRoutes")) {
            $this->loadRoutesFrom(__DIR__ . "/routes/admin/specification.php");
        }
        // Управление группами характеристик.
        if (config("category-product.specificationGroupAdminRoutes")) {
            $this->loadRoutesFrom(__DIR__ . "/routes/admin/specification-group.php");
        }
        // Управление метками товаров.
        if (config("category-product.productLabelAdminRoutes")) {
            $this->loadRoutesFrom(__DIR__ . "/routes/admin/product-label.php");
        }
        // Управление коллекциями товаров.
        if (config("category-product.productCollectionAdminRoutes")) {
            $this->loadRoutesFrom(__DIR__ . "/routes/admin/product-collection.php");
        }
        // Управление товарами.
        if (config("category-product.productAdminRoutes")) {
            $this->loadRoutesFrom(__DIR__ . "/routes/admin/product.php");
        }
        // Значения характеристик.
        if (config("category-product.productSpecificationAdminRoutes")) {
            $this->loadRoutesFrom(__DIR__ . "/routes/admin/product-specification.php");
        }
        // Управление типами дополнений.
        if (config("category-product.addonTypesAdminRoutes")) {
            $this->loadRoutesFrom(__DIR__ . "/routes/admin/addon-type.php");
        }
    }

    /**
     * Добавить настройки для каталога в другие пакеты.
     */
    protected function extendConfigVariables()
    {
        // SVG.
        $svg = app()->config["theme.configSvg"];
        $svg[] = "category-product::site.includes.svg";
        app()->config["theme.configSvg"] = $svg;

        // Подключение метатегов.
        $seo = app()->config["seo-integration.models"];
        $seo["categories"] = Category::class;
        $seo["products"] = Product::class;
        $seo["product_collection"] = ProductCollection::class;
        app()->config["seo-integration.models"] = $seo;

        // Подключаем изображения.
        $imagecache = app()->config['imagecache.paths'];
        $imagecache[] = 'storage/categories';
        $imagecache[] = 'storage/product_collections';
        $imagecache[] = 'storage/gallery/products';
        app()->config['imagecache.paths'] = $imagecache;

        // Подключаем галерею.
        $gallery = app()->config["gallery.models"];
        $gallery["products"] = Product::class;
        app()->config["gallery.models"] = $gallery;

        // Фильтры изображений.
        $imagecache = app()->config["imagecache.templates"];
        // Тизеры.
        if (config("category-product.useSimpleTeaser")) {
            if (empty($imagecache["catalog-teaser-xl"])) $imagecache["catalog-teaser-xl"] = CatalogSimpleTeaserXl::class;
            if (empty($imagecache["catalog-teaser-lg"])) $imagecache["catalog-teaser-lg"] = CatalogSimpleTeaserLg::class;
            if (empty($imagecache["product-teaser-lg"])) $imagecache["product-teaser-lg"] = ProductSimpleTeaserLg::class;
            if (empty($imagecache["catalog-teaser-md"])) $imagecache["catalog-teaser-md"] = CatalogSimpleTeaserMd::class;
            if (empty($imagecache["catalog-teaser-sm"])) $imagecache["catalog-teaser-sm"] = CatalogSimpleTeaserSm::class;
            if (empty($imagecache["catalog-teaser-xs"])) $imagecache["catalog-teaser-xs"] = CatalogSimpleTeaserXs::class;

            if (empty($imagecache["product-show-xl"])) $imagecache["product-show-xl"] = ProductSimpleShowXl::class;
            if (empty($imagecache["product-show-lg"])) $imagecache["product-show-lg"] = ProductSimpleShowLg::class;
            if (empty($imagecache["product-show-md"])) $imagecache["product-show-md"] = ProductSimpleShowMd::class;
            if (empty($imagecache["product-show-sm"])) $imagecache["product-show-sm"] = ProductSimpleShowSm::class;

            if (empty($imagecache["product-show-thumb"])) $imagecache["product-show-thumb"] = ProductThumbSimpleShow::class;
        }
        else {
            if (empty($imagecache["catalog-teaser-xl"])) $imagecache["catalog-teaser-xl"] = CatalogTeaserXl::class;
            if (empty($imagecache["catalog-teaser-lg"])) $imagecache["catalog-teaser-lg"] = CatalogTeaserLg::class;
            if (empty($imagecache["product-teaser-lg"])) $imagecache["product-teaser-lg"] = ProductTeaserLg::class;
            if (empty($imagecache["catalog-teaser-md"])) $imagecache["catalog-teaser-md"] = CatalogTeaserMd::class;
            if (empty($imagecache["catalog-teaser-sm"])) $imagecache["catalog-teaser-sm"] = CatalogTeaserSm::class;
            if (empty($imagecache["catalog-teaser-xs"])) $imagecache["catalog-teaser-xs"] = CatalogTeaserXs::class;

            if (empty($imagecache["product-show-xl"])) $imagecache["product-show-xl"] = ProductShowXl::class;
            if (empty($imagecache["product-show-lg"])) $imagecache["product-show-lg"] = ProductShowLg::class;
            if (empty($imagecache["product-show-md"])) $imagecache["product-show-md"] = ProductShowMd::class;
            if (empty($imagecache["product-show-sm"])) $imagecache["product-show-sm"] = ProductShowSm::class;

            if (empty($imagecache["product-show-thumb"])) $imagecache["product-show-thumb"] = ProductThumbShow::class;
        }
        app()->config['imagecache.templates'] = $imagecache;
    }
}
