<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            $table->string("title")
                ->comment("Заголовок");
            
            $table->string("slug")
                ->unique()
                ->comment("Адресная строка");
            
            $table->unsignedBigInteger("category_id")
                ->comment("Категория");
            
            $table->string("short")
                ->nullable()
                ->comment("Краткое описание");
            
            $table->text("description")
                ->nullable()
                ->comment("Описание");
            
            $table->dateTime("published_at")
                ->nullable()
                ->comment("Статус публикации");
            
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
        Schema::dropIfExists('products');
    }
}
