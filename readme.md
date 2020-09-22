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

Шаблон для меню:
    
    category-product::site.includes.categories-menu

Выгрузка конфигурации:
    
    php artisan vendor:publish --provider="PortedCheese\CategoryProduct\ServiceProvider" --tag=config

Переменные конфигурации:

    categoryAdminRoutes(true) - Использовать роуты для управления категориями из пакета
    categoryShowRouteName(admin.categories.show) - Адрес просмотра категории
    specificationAdminRoutes(true) - Использовать роуты для управления характеристиками из пакета
    specificationGroupAdminRoutes(true) - Использовать роуты для управления группами характеристик из пакета
    productLabelAdminRoutes(true) - Использовать роуты для управления метками товара из пакета
    productAdminRoutes(true) - Использовать роуты для управления товарами из пакета
    productSpecificationAdminRoutes(true) - Использовать роуты для управления значениями характеристик из пакета
    
    categoryFacade - Класс для фасада действий с категориями
    specificationFacade - Класс для фасада действий с характеристиками
    productFacade - Класс для фасада действий с товарами
    productFilterFacade - Класс для фасада фильтрации товара
    productFavoriteFacade - Класс для фасада избранных товаров
    
    categorySiteRoutes(true) - Роуты категорий для сайта
    subCategoriesPage(false) - Включить страницу подкатегорий
    categoryProductsPerPage(18) - Товаров на страницу
    productSiteRoutes(true) - Роуты товаров для сайта
    defaultProductView(bar) - Отображение товаров по умолчанию
    useSimpleTeaser(true) - Использовать изображение, которые убдут обрезаны по размеру. Если переключить изображения не будут обрезаться
    catalogPageName(Каталог) - Заголовок страницы каталога
    categoryNest(3) - максимальная вложенность категорий
    
    defaultSort(title) - Сортировка товаров по умолчанию
    defaultSortDirection(asc) - Стандартное направление сортировки
    defaultCheckboxLimit(3) - Сколько элементов группы чекбоксов в фильтре будут показаны, больше будут скрыты и появится поиск
    sortOptions - Список сортировок
    
### Versions

    v1.0.5:
        - Добавлены стили для слайдера и задержна на загрузку
        - Добавлена синхронизация названий характеристик
    Обновление:
        - php artisan vendor:publish --provider="PortedCheese\CategoryProduct\ServiceProvider" --tag=public --force
        - npm run prod