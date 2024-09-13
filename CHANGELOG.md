## Versions

###v3.0.0: base-settings 5.0 (bootstrap 5)
Добавлены:

- Фильтры CatalogSimpleTeaserXxl & CatalogTeaserXxl 

Проверить переопределение:
- components > addProductSpecificationValue, editProductSPecificationValue, FavoriteState
- sass > catalog, product, favorite
- admin views > menu, addon-types.includes.table-listm categories.includes.pills, product-collections.includes.table-list, product-labels.index, products.index, products.show, products.includes.pills, products.includes.product-filter, specification-groups.index, specifications.listm
- site views > categories.includes.teaser, includes.cvategories-menu, includes.svg, product-collections.includes.teaser, products.show, products.includes.*

Обновление


    php artisan vendor:publish --provider="PortedCheese\CategoryProduct\ServiceProvider" --tag=public --force
    php artisan config:clear
    php artisan cache:clear
    php artisan image-filters:clear

###v2.0.0-v2.0.2: Дополнительные товары, Типы дополнений. 

Товар с указанным Типом дополнения является Дополнением.
Вывод и фильтрация дополнений  в карточке товара (по значениям хаарктеристик).

Новые параметры конфига:

        addonTypesAdminRoutes [true|false]
        useAddons [true|false]
        addonTypesName ["Типы дополнений"]
        addonsName ["Дополнения"]
        enableFilterAddons[true|false]  // need cache:clear command

Добавлены:
- миграции: addon_types_table, type_id_to_products_table
- AddonType - модель, политика, контроллер для типов дополнений
- Методы кэша: CategoryActions > getCategoryParentsIds, forgetCategoryParentsIds, ProductActions > getProductAddons, forgetProductAddons
- Models/Category > addons() : HasMany | null
- Models/Product > addonType: BelongsTo
- Events/CategoriesAddonsUpdate(), Listeners/CategoriesAddonsClearCache
- scss: product-show__addon class
- vue: categoryProductEventBus
- Admin Controllers: AddonTypeController
- admin views: addon-types.*, addon-types.includes.*, products.create-addon, products.edit-addon, products,includes.product-filter
- site views: products.includes.addons, profuc.includes.addon-teaser
- routes/admin/addon-type

Проверить переорпределение:

- ProductActionsManager > getCategoryProductIds (enableFilterAddons, false), getYouWatch (исключить допы)
- ProductFilterManager > initQuery() (enableFilterAddons, false)
- Admin/ProductController > index, create, edit ...
- Site/ProductController > show ($adddonsArray для товара или redirect to Category для Дополнения)
- Models/Category > products()
- ProductObserver > updated(), deleted()
- ProductSpecificationObserver > updated(), deleting()
- vue: FavoriteProduct, FavoriteState (categoryProductEventBus)
- views.admin: menu, categories.includes.breadcrumb, products.index, products.includes.pills
- views.site: products.show > useAddons
- routes/admin/product: categories.addons


Обновление:


        php artisan migrate
        php artisan make:category-product --models /create AddonType Model 
        php artisan make:category-product --policies /create AddonTypePolicy 
        php artisan make:category-product --controllers /create Admin/AddonTypeController
        php artisan vendor:publish --provider="PortedCheese\CategoryProduct\ServiceProvider" --tag=public --force
        php artisan cache:clear
        php artisan config:clear
        php artisan queue:restart

         
###v1.6.0: Использование характеристик товара в вариациях (product-variation ^1.3) & jquery-bridget

Проверить переопределение:

- Model/ProductSpecification > variations(): false | variations
- Надблюдатель ProductSpecificationObserver
- Шаблон site/products/includes/show-top-section (image: add cell[id] class)
        
Обновление:

        php artisan make:category-product --observers
        npm i jquery-bridget (use ProdfuctFlickity to selcetCell)
        npm run
        php artisan cache:clear
###v1.5.0: Добавлено поле code (цвет) к характеристикам товара

Проверить переопределение:
- Models/ProductSpecification > fillable, Models/Specification  > types
- Controllers/Admin/ProductSpecificationController > create, validator, current
- vue components: ProductSpecificationsComponent, AddProductSpecificationValueComponent, EditProductSpecificaitonValue, FilterCheckboxComponent
- helpers: ProductActionsManager & ProductFilterManager
- views: site.filters.form
        
Обновление:

        php artisan migrate
        php artisan vendor:publish --provider="PortedCheese\CategoryProduct\ServiceProvider" --tag=public --force
        php artisan cache:clear (очитска кэша фильтров категорий)
        npm run
###v1.4.0: Добавлено Measurement в  меню админики

###v1.3.0 - v1.3.4: Коллекции товаров: управление, отображение + настройки конфига для коллекций
    
Проверить переопределение:
- Модели Product (связка, очистка кэша коллекций)
- ProductObserver (очистка коллекций)
- Admin/ProductController > create, edit, show
- Шаблонов admin.menu & admin.products.create&edit&show
- Шаблона site.products.includes.show-top-section (вывод подборок)
   
Обновление:

       php artisan migrate
       php artisan make:category-products --models --observers --controllers --policies  (создать коллекции)

###v1.2.6-1.2.7: Актуализация Избранного (при открытии избранных товаров), очистка от удаленных товаров
    
Проверить переопределение:
- Site/ProductController > favoritelist()
- Фасада ProductFavoriteManager

###v1.2.4, 1.2.5: вывод неопубликованных товаров в избранном 
   
Проверить переопределение:
- тизера товара site.products.includes.teaser
- метода Site/ProductController > favoriteList
    
Обновление:

        php artisan vendor:publish --provider="PortedCheese\CategoryProduct\ServiceProvider" --tag=public --force
        npm run prod

###v1.2.3: обновление выборки дерева категорий для меню каталога
Проверить переопределение:
- CategoryActions > getTree, makeTreeDataWithNoParent 

###v1.2.0-v1.2.2: Публикация/Снятие с публикации Категории
- add published_at, default value = now()
- Добавлены права на публикацию
- Если снять с публикации категорию - снимаются с публикации все подкатегории и товары
- Невозможно опубликовать товар в неопубликованной категории
- Невозможно опубликовать подкатегории, если родитель снять с публикации
    
Обновление:

        php artisan migrate   (добавит поле публикации с текущей датой ко всем категориям проекта)
        - если в проекте есть выборки категорий, доавить в выборку проверку статуса публикации

Проверить переопределение:
- ProductActionsManager, Admin/ParoductController > changePublished, Site/CategoryController > index,show, CategoryObserver > creationg, updated
- шаблонов: admin.catrgories.includes.table-list

###v1.1.2:  base 4.0

###v1.1.1: Описание к категории

- Добавлено описание к категории
- В метках, избранном и блоке "вы смотрели" не показываются снятые с публикации товары (дополнен ProductController & ProductActionManager)

Обновление:

    php artisan migrate

Проверить переопределение:
- шаблонов категории (admin.catefories.create, admin.categories.edit, admin.categories.show, site.categories.show) 
- модели Category > fillable, ProductController > label & favouriteList,  ProductActionManager > getYouWatch

###v1.1.0: base-settings 3.0 & fix productFilter

###v1.0.5:

- Добавлены стили для слайдера и задержна на загрузку
- Добавлена синхронизация названий характеристик

Обновление:

        php artisan vendor:publish --provider="PortedCheese\CategoryProduct\ServiceProvider" --tag=public --force
        npm run prod