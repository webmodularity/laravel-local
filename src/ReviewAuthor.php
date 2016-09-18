<?php

namespace WebModularity\LaravelLocal;

use Illuminate\Database\Eloquent\Model;

class ReviewAuthor extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['review_provider_id', 'review_provider_author_id', 'name', 'url_image'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the Review record associated with the ReviewAuthor.
     */
    public function review()
    {
        return $this->hasOne('WebModularity\LaravelLocal\Review');
    }


    /**
     * Get the ReviewProvider that owns this ReviewAuthor.
     */
    public function reviewProvider()
    {
        return $this->belongsTo('WebModularity\LaravelProviders\ReviewProvider');
    }

    public function getUrlAttribute() {
        return str_replace('{user_id}', $this->review_provider_author_id, $this->reviewProvider->url_review_user);
    }
}