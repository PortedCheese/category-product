<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSpecificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_specification', function (Blueprint $table) {
            $table->unsignedBigInteger("specification_id")
                ->comment("Характеристика");

            $table->unsignedBigInteger("product_id")
                ->comment("Товар");

            $table->unsignedBigInteger("category_id")
                ->comment("Категория");

            $table->json("values")
                ->nullable()
                ->comment("Значения");

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
