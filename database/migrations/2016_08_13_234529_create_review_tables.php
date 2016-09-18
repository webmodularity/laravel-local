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
        Schema::create('review_authors', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallInteger('review_provider_id')->unsigned();
            $table->string('review_provider_author_id', 30);
            $table->string('name', 100);
            $table->string('url_image', 255)->nullable();
            $table->unique(['review_provider_id', 'review_provider_author_id'], 'provider_provider_author_unique');
            $table->foreign('review_provider_id', 'review_providers_id_review_authors_review_provider_id')->references('id')->on('common.review_providers')->onUpdate('cascade');
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallInteger('author_id')->unsigned();
            $table->string('review_id', 50)->nullable();
            $table->boolean('active')->default(false);
            $table->tinyInteger('rating')->default(1);
            $table->text('review');
            $table->boolean('is_excerpt')->default(false);
            $table->timestamp('review_created_at')->nullable();
            $table->timestamp('review_updated_at')->nullable();
            $table->timestamps();
            $table->index(['active', 'rating', 'review_updated_at']);
            $table->index(['active', 'review_updated_at']);
            $table->foreign('author_id', 'review_authors_id_reviews_author_id')->references('id')->on('review_authors')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('reviews');
        Schema::drop('review_authors');
    }
}
