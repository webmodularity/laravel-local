<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('local_review_authors', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallInteger('source_id')->unsigned();
            $table->string('source_author_id', 30);
            $table->string('name', 100);
            $table->string('url_image', 255)->nullable();
            $table->unique(['source_id', 'source_author_id']);
            $table->foreign('source_id')->references('id')->on('common.local_sources')->onUpdate('cascade');
        });

        Schema::create('local_reviews', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallInteger('author_id')->unsigned();
            $table->string('source_review_id', 50)->nullable();
            $table->boolean('active')->default(false);
            $table->tinyInteger('rating')->default(1);
            $table->text('review');
            $table->boolean('is_excerpt')->default(false);
            $table->timestamp('review_created_at')->nullable();
            $table->timestamp('review_updated_at')->nullable();
            $table->timestamps();
            $table->index(['active', 'rating', 'review_updated_at']);
            $table->index(['active', 'review_updated_at']);
            $table->foreign('author_id')->references('id')->on('local_review_authors')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('local_reviews');
        Schema::drop('local_review_authors');
    }
}
