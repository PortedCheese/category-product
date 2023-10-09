<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPublishedAtToCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('categories', 'published_at')){
            Schema::table('categories', function (Blueprint $table) {
                $table
                    ->dateTime('published_at')
                    ->nullable()
                    ->default(now())
                    ->after("priority")
                ;
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn("published_at");
        });
    }
}
