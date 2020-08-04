<?php

namespace PortedCheese\CategoryProduct;

use App\Category;
use PortedCheese\CategoryProduct\Console\Commands\CategoryProductMakeCommand;
use PortedCheese\CategoryProduct\Helpers\CategoryActionsManager;
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
            __DIR__ . "/resources/sass" => resource_path("sass/vendor")
        ], 'public');
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
    }

    /**
     * Подключение путей.
     */
    protected function addRoutes()
    {
        if (config("category-product.categoryAdminRoutes")) {
            $this->loadRoutesFrom(__DIR__ . "/routes/admin/category.php");
        }
        if (config("category-product.specificationAdminRoutes")) {
            $this->loadRoutesFrom(__DIR__ . "/routes/admin/specification.php");
        }
        if (config("category-product.specificationGroupAdminRoutes")) {
            $this->loadRoutesFrom(__DIR__ . "/routes/admin/specification-group.php");
        }
        if (config("category-product.productLabelAdminRoutes")) {
            $this->loadRoutesFrom(__DIR__ . "/routes/admin/product-labels.php");
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
        app()->config["seo-integration.models"] = $seo;

        // Подключаем изображения.
        $imagecache = app()->config['imagecache.paths'];
        $imagecache[] = 'storage/categories';
        app()->config['imagecache.paths'] = $imagecache;
    }
}
