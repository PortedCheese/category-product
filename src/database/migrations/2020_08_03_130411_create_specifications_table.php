<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specifications', function (Blueprint $table) {
            $table->id();
            
            $table->string("title")
                ->comment("Заголовок");
            
            $table->string("slug")
                ->unique()
                ->comment("Ключ");
            
            $table->unsignedBigInteger("group_id")
                ->nullable()
                ->comment("Группа");
            
            $table->string("type")
                ->comment("Тип характеристики");
            
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
        Schema::dropIfExists('specifications');
    }
}
