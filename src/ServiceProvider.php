<?php

namespace PortedCheese\CategoryProduct;

use App\Category;
use PortedCheese\CategoryProduct\Console\Commands\CategoryProductMakeCommand;

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
    }

    public function register()
    {
        $this->mergeConfigFrom(
          __DIR__ . '/config/category-product.php', 'category-product'
        );
    }

    /**
     * Подключение путей.
     */
    protected function addRoutes()
    {
        if (config("category-product.categoryAdminRoutes")) {
            $this->loadRoutesFrom(__DIR__ . "/routes/admin/category.php");
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
