<?php

namespace PortedCheese\CategoryProduct;

use App\Category;
use App\Observers\Vendor\CategoryProduct\CategoryObserver;
use App\Observers\Vendor\CategoryProduct\ProductLabelObserver;
use App\Observers\Vendor\CategoryProduct\ProductObserver;
use App\Observers\Vendor\CategoryProduct\SpecificationGroupObserver;
use App\Product;
use App\ProductLabel;
use App\SpecificationGroup;
use PortedCheese\CategoryProduct\Console\Commands\CategoryProductMakeCommand;
use PortedCheese\CategoryProduct\Helpers\CategoryActionsManager;
use PortedCheese\CategoryProduct\Helpers\ProductActionsManager;
use PortedCheese\CategoryProduct\Helpers\SpecificationActionManager;

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
            __DIR__ . "/resources/sass" => resource_path("sass/vendor/category-product")
        ], 'public');

        // Наблюдатели.
        $this->addObservers();
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
        if (class_exists(SpecificationGroupObserver::class) && class_exists(SpecificationGroup::class)) {
            SpecificationGroup::observe(SpecificationGroupObserver::class);
        }
    }

    /**
     * Подключение Facades.
     */
    protected function initFacades()
    {
        $this->app->singleton("category-actions", function () {
            return new CategoryActionsManager;
        });

        $this->app->singleton("specification-actions", function () {
            return new SpecificationActionManager;
        });

        $this->app->singleton("product-actions", function () {
            return new ProductActionsManager;
        });
    }

    /**
     * Подключение путей.
     */
    protected function addRoutes()
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
        // Управление товарами.
        if (config("category-product.productAdminRoutes")) {
            $this->loadRoutesFrom(__DIR__ . "/routes/admin/product.php");
        }
        // Значения характеристик.
        if (config("category-product.productSpecificationAdminRoutes")) {
            $this->loadRoutesFrom(__DIR__ . "/routes/admin/product-specification.php");
        }
    }

    /**
     * Добавить настройки для каталога в другие пакеты.
     */
    protected function extendConfigVariables()
    {
        // Подключение метатегов.
        $seo = app()->config["seo-integration.models"];
        $seo["categories"] = Category::class;
        $seo["products"] = Product::class;
        app()->config["seo-integration.models"] = $seo;

        // Подключаем изображения.
        $imagecache = app()->config['imagecache.paths'];
        $imagecache[] = 'storage/categories';
        $imagecache[] = 'storage/gallery/products';
        app()->config['imagecache.paths'] = $imagecache;

        // Подключаем галерею.
        $gallery = app()->config["gallery.models"];
        $gallery["products"] = Product::class;
        app()->config["gallery.models"] = $gallery;
    }
}
