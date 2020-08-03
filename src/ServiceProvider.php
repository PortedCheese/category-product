<?php

namespace PortedCheese\CategoryProduct;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

  public function boot()
  {
      $this->publishes([
          __DIR__ . '/config/category-product.php' => config_path('category-product.php'),
      ], 'config');
  }

  public function register()
  {
      $this->mergeConfigFrom(
          __DIR__ . '/config/category-product.php', 'category-product'
      );
  }
}
