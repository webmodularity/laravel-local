<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('local_hours', function (Blueprint $table) {
            $table->tinyInteger('weekday_index')->unsigned();
            $table->time('time_start');
            $table->time('time_end')->nullable();
            $table->primary(['weekday_index', 'time_start']);
            $table->unique(['weekday_index', 'time_start', 'time_end']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('local_hours');
    }
}
