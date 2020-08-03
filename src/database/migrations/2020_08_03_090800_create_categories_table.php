<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            $table->string("title")
                ->comment("Заголовок категории");

            $table->string("slug")
                ->unique()
                ->comment("Адресная строка");

            $table->string("short")
                ->nullable()
                ->comment("Краткое описание");

            $table->unsignedBigInteger("parent_id")
                ->nullable()
                ->comment("Родительская категория");

            $table->unsignedBigInteger("image_id")
                ->nullable()
                ->comment("Изображение");

            $table->unsignedBigInteger("priority")
                ->default(0)
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
        Schema::dropIfExists('categories');
    }
}
