<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategorySpecificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_specification', function (Blueprint $table) {
            $table->unsignedBigInteger("category_id")
                ->comment("Категория");

            $table->unsignedBigInteger("specification_id")
                ->comment("Характеристика");

            $table->string("title")
                ->comment("Заголвок");

            $table->tinyInteger("filter")
                ->default(0)
                ->comment("В фильтре");

            $table->unsignedBigInteger("priority")
                ->default(1)
                ->comment("Приоритет");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category_specification');
    }
}
