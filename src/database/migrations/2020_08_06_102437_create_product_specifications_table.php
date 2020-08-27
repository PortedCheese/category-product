<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSpecificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_specifications', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("specification_id")
                ->comment("Характеристика");

            $table->unsignedBigInteger("product_id")
                ->comment("Товар");

            $table->unsignedBigInteger("category_id")
                ->comment("Категория");

            $table->string("value")
                ->comment("Значение");

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
        Schema::dropIfExists('product_specification');
    }
}
