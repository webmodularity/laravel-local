<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommonLocalSources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('common.local_sources')) {
            Schema::create('common.local_sources', function (Blueprint $table) {
                $table->smallIncrements('id');
                $table->string('name', 50);
                $table->string('url', 255);
                $table->string('url_review', 255)->nullable();
                $table->string('url_review_user', 255)->nullable();
                $table->unique(['name']);
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
        // This common table should be dropped by hand
        // Schema::drop('common.local_sources');
    }
}
