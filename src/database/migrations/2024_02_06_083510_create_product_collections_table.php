<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_collections', function (Blueprint $table) {
            $table->id();
            
            $table->string("title")
                ->comment("Заголовок");
            
            $table->string("slug")
                ->unique()
                ->comment("Адресная строка");

            $table->text('short')
                ->nullable();
            $table->longText('description')
                ->nullable();

            $table->unsignedBigInteger("image_id")
                ->nullable()
                ->comment("Изображение");

            $table->unsignedBigInteger("priority")
                ->default(0)
                ->comment("Приоритет");

            $table->dateTime('published_at')
                ->nullable()
                ->default(now());

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
        Schema::dropIfExists('product_collections');
    }
}
