# Category Product

## Description

Какое-то описание

## Install
    php artisan migrate

    php artisan vendor:publish --provider="PortedCheese\CategoryProduct\ServiceProvider" --tag=public --force

    php artisan make:catalog
                            {--all : Run all}
                            {--menu : Config menu}
                            {--models : Export models}
                            {--controllers : Export controllers}
                            {--policies : Export and create rules}
                            {--only-default : Create default rules}
                            {--vue : Export vue}
                            
## Config
    
    categoryAdminRoutes - Использовать роуты для управления категориями из пакета
    categoryShowRouteName - Адрес просмотра категории
    specificationAdminRoutes - Использовать роуты для управления характеристиками из пакета