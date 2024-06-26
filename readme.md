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
    productCollectionAdminRoutes - Использовать роуты для управления коллекциями товара из пакета
    productAdminRoutes(true) - Использовать роуты для управления товарами из пакета
    productSpecificationAdminRoutes(true) - Использовать роуты для управления значениями характеристик из пакета
    
    categoryFacade - Класс для фасада действий с категориями
    specificationFacade - Класс для фасада действий с характеристиками
    productFacade - Класс для фасада действий с товарами
    productFilterFacade - Класс для фасада фильтрации товара
    productFavoriteFacade - Класс для фасада избранных товаров
    
    productSpecificationResource - Класс для ресурса характеристик
    
    categorySiteRoutes(true) - Роуты категорий для сайта
    subCategoriesPage(false) - Включить страницу подкатегорий
    categoryProductsPerPage(18) - Товаров на страницу
    productSiteRoutes(true) - Роуты товаров для сайта
    productCollectionSiteRoutes (true) - Роуты коллекций товаров для сайта
    defaultProductView(bar) - Отображение товаров по умолчанию
    useSimpleTeaser(true) - Использовать изображение, которые будут обрезаны по размеру. Если переключить изображения не будут обрезаться
    catalogPageName(Каталог) - Заголовок страницы каталога
    productCollectionsName(Коллекции) - Заголовок страницы Коллекций
    categoryNest(3) - максимальная вложенность категорий
    
    defaultSort(title) - Сортировка товаров по умолчанию
    defaultSortDirection(asc) - Стандартное направление сортировки
    defaultCheckboxLimit(3) - Сколько элементов группы чекбоксов в фильтре будут показаны, больше будут скрыты и появится поиск
    sortOptions - Список сортировок
    
### Versions
    v1.6.0: Использование характеристик товара в вариациях (product-variation ^1.3) & jquery-bridget
        Проверить переопределение:
        - Model/ProductSpecification > variations(): false | variations
        - Надблюдатель ProductSpecificationObserver
        - Шаблон site/products/includes/show-top-section (image: add cell[id] class)
        Обновление:
        - php artisan make:category-product --observers
        - npm i jquery-bridget (use ProdfuctFlickity to selcetCell)
        - npm run
        - php artisan cache:clear
    v1.5.0: Добавлено поле code (цвет) к характеристикам товара
        Проверить переопределение:
        - Models/ProductSpecification > fillable, Models/Specification  > types
        - Controllers/Admin/ProductSpecificationController > create, validator, current
        - vue components: ProductSpecificationsComponent, AddProductSpecificationValueComponent, EditProductSpecificaitonValue, FilterCheckboxComponent
        - helpers: ProductActionsManager & ProductFilterManager
        - views: site.filters.form
        Обновление:
        - php artisan migrate
        - php artisan vendor:publish --provider="PortedCheese\CategoryProduct\ServiceProvider" --tag=public --force
        - php artisan cache:clear (очитска кэша фильтров категорий)
        - npm run
    v1.4.0: Добавлено Measurement в  меню админики
    v1.3.0 - v1.3.4:
        - Коллекции товаров: управление, отображение + настройки конфига для коллекций
    Проверить переопределение:
        - Модели Product (связка, очистка кэша коллекций)
        - ProductObserver (очистка коллекций)
        - Admin/ProductController > create, edit, show
        - Шаблонов admin.menu & admin.products.create&edit&show
        - Шаблона site.products.includes.show-top-section (вывод подборок)
    Обновление:
        - php artisan migrate
        - php artisan make:category-products --models --observers --controllers --policies  (создать коллекции)
    v1.2.6-1.2.7:
        - Актуализация Избранного (при открытии избранных товаров), очистка от удаленных товаров
    Проверить переопределение:
        - Site/ProductController > favoritelist()
        - Фасада ProductFavoriteManager
    v1.2.4, 1.2.5:
        - вывод неопубликованных товаров в избранном 
    Проверить переопределение:
        - тизера товара site.products.includes.teaser
        - метода Site/ProductController > favoriteList
    Обновление:
        - php artisan vendor:publish --provider="PortedCheese\CategoryProduct\ServiceProvider" --tag=public --force
        - npm run prod
    v1.2.3:
        - обновление выборки дерева категорий для меню каталога
    Проверить переопределение:
        - CategoryActions > getTree, makeTreeDataWithNoParent 
    v1.2.0-v1.2.2:
        - Публикация/Снятие с публикации Категории (add published_at, default value = now())
        - Добавлены права на публикацию
        - Если снять с публикации категорию - снимаются с публикации все подкатегории и товары
        - Невозможно опубликовать товар в неопубликованной категории
        - Невозможно опубликовать подкатегории, если родитель снять с публикации
    Обновление:
        - php artisan migrate   (добавит поле публикации с текущей датой ко всем категориям проекта)
        - если в проекте есть выборки категорий, доавить в выборку проверку статуса публикации
    Проверить переопределение:
        - ProductActionsManager, Admin/ParoductController > changePublished, Site/CategoryController > index,show, CategoryObserver > creationg, updated
        - шаблонов: admin.catrgories.includes.table-list

    v1.1.2: 
        - base 4.0
    v1.1.1:
        - Добавлено описание к категории
        - В метках, избранном и блоке "вы смотрели" не показываются снятые с публикации товары (дополнен ProductController & ProductActionManager)
    Обновление:
        - php artisan migrate
        - проверить переопределение шаблонов категории (admin.catefories.create, admin.categories.edit, admin.categories.show, site.categories.show) 
        - Проверить переопределение модели Category > fillable, ProductController > label & favouriteList,  ProductActionManager > getYouWatch
    v1.1.0:
        - base-settings 3.0 & fix productFilter
    v1.0.5:
        - Добавлены стили для слайдера и задержна на загрузку
        - Добавлена синхронизация названий характеристик
    Обновление:
        - php artisan vendor:publish --provider="PortedCheese\CategoryProduct\ServiceProvider" --tag=public --force
        - npm run prod