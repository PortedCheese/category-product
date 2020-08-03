<?php

namespace PortedCheese\CategoryProduct\Console\Commands;

use App\Menu;
use App\MenuItem;
use PortedCheese\BaseSettings\Console\Commands\BaseConfigModelCommand;

class CategoryProductMakeCommand extends BaseConfigModelCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "make:category-product
                    {--all : Run all}
                    {--menu : Config menu}
                    {--models : Export models}
                    {--controllers : Export controllers}
                    {--policies : Export and create rules}
                    {--only-default : Create only default rules}
                    {--vue : Export vue}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Settings for categories and products";

    /**
     * Имя пакета.
     *
     * @var string
     */
    protected $packageName = "CategoryProduct";

    /**
     * The models that need to be exported.
     * @var array
     */
    protected $models = ["Category"];

    /**
     * Создание контроллеров.
     *
     * @var array
     */
    protected $controllers = [
        "Admin" => ["CategoryController"],
        "Site" => [],
    ];

    /**
     * Папка для vue файлов.
     *
     * @var string
     */
    protected $vueFolder = "category-product";

    /**
     * Список vue файлов.
     *
     * @var array
     */
    protected $vueIncludes = [
        'admin' => [
            'admin-category-list' => "CategoryListComponent",
        ],
        'app' => [],
    ];

    /**
     * Политики.
     *
     * @var array
     */
    protected $ruleRules = [
        [
            "title" => "Категории",
            "slug" => "categories",
            "policy" => "CategoryPolicy",
        ]
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $all = $this->option("all");

        if ($this->option('menu') || $all) {
            $this->makeMenu();
        }

        if ($this->option("models") || $all) {
            $this->exportModels();
        }

        if ($this->option("controllers") || $all) {
            $this->exportControllers("Admin");
            $this->exportControllers("Site");
        }

        if ($this->option("vue") || $all) {
            $this->makeVueIncludes("admin");
            $this->makeVueIncludes("app");
        }

        if ($this->option("policies") || $all) {
            $this->makeRules();
        }
    }

    /**
     * Создать меню.
     */
    protected function makeMenu()
    {
        try {
            $menu = Menu::query()->where("key", "admin")->firstOrFail();
        }
        catch (\Exception $exception) {
            return;
        }

        $title = "Каталог";
        $itemData = [
            "title" => $title,
            "menu_id" => $menu->id,
            "url" => "#",
            "template" => "category-product::admin.menu",
        ];

        try {
            $menuItem = MenuItem::query()
                ->where("menu_id", $menu->id)
                ->where("title", "$title")
                ->firstOrFail();
            $this->info("Menu item '{$title}' was updated");
        }
        catch (\Exception $exception) {
            MenuItem::create($itemData);
            $this->info("Menu item '{$title}' was created");
        }
    }
}