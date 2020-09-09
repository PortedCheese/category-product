# Category Product

## Description

Категории и товары для сайта.

Категории могут быть вложенными, у каждой категории есть набор характеристик, которые можно синхронизировать на дочернии категории.

Товар относится к категори, может быть с метками и значениями характеристик от категории.

## Install
    php artisan migrate

    php artisan vendor:publish --provider="PortedCheese\CategoryProduct\ServiceProvider" --tag=public --force

    php artisan make:category-product
                            {--all : Run all}
                            {--menu : Config menu}
                            {--models : Export models}
                            {--observers : Export observers}
                            {--controllers : Export controllers}
                            {--policies : Export and create rules}
                            {--only-default : Create default rules}
                            {--scss : Export scss}
                            {--vue : Export vue}
                            {--js : Export scripts}
                            
    npm install flickity
    npm install flickity-as-nav-for
                            
## Config

Если надо выгрузить конфиг:
    
    php artisan vendor:publish --provider="PortedCheese\CategoryProduct\ServiceProvider" --tag=config

Переменные конфига:

    categoryAdminRoutes - Использовать роуты для управления категориями из пакета
    categoryShowRouteName - Адрес просмотра категории
    specificationAdminRoutes - Использовать роуты для управления характеристиками из пакета
    specificationGroupAdminRoutes - Использовать роуты для управления группами характеристик из пакета
    productLabelAdminRoutes - Использовать роуты для управления метками товара из пакета
    productAdminRoutes - Использовать роуты для управления товарами из пакета
    productSpecificationAdminRoutes - Использовать роуты для управления значениями характеристик из пакета
    
    categoryFacade - Класс для фасада действий с категориями
    specificationFacade - Класс для фасада действий с характеристиками
    productFacade - Класс для фасада действий с товарами
    productFilterFacade - Класс для фасада фильтрации товара
    
    categorySiteRoutes - Роуты категорий для сайта
    subCategoriesPage - Включить страницу подкатегорий
    categoryProductsPerPage - Товаров на страницу
    productSiteRoutes - Роуты товаров для сайта