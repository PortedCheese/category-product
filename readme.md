# Category Product

## Description

Какое-то описание

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
                            
## Config

Если надо выгрузить конфиг:
    
    php artisan vendor:publish --provider="PortedCheese\CategoryProduct\ServiceProvider" --tag=config

Переменные конфига:

    categoryAdminRoutes - Использовать роуты для управления категориями из пакета
    categoryShowRouteName - Адрес просмотра категории
    specificationAdminRoutes - Использовать роуты для управления характеристиками из пакета