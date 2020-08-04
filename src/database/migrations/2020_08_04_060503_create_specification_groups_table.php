<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecificationGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specification_groups', function (Blueprint $table) {
            $table->id();
            
            $table->string("title")
                ->comment("Заголовок");
            
            $table->string("slug")
                ->unique()
                ->comment("Ключ");
            
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
        Schema::dropIfExists('specification_groups');
    }
}
