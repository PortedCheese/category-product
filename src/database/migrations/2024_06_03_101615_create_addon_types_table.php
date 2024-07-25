<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddonTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addon_types', function (Blueprint $table) {
            $table->id();

            $table->string("title")
                ->comment("Заголовок дополнения");

            $table->string("slug")
                ->unique()
                ->comment("Адресная строка");

            $table->string("short")
                ->nullable()
                ->comment("Краткое описание");

            $table->longText("description")
                ->nullable()
                ->comment("Описание");

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
        Schema::dropIfExists('addon_types');
    }
}
