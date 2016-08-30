<?php

namespace WebModularity\LaravelLocal;

use Illuminate\Database\Eloquent\Model;

class ReviewAuthor extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'local_review_authors';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['source_id', 'source_author_id', 'name', 'url_image'];

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
     * Get the ReviewSource that owns this ReviewAuthor.
     */
    public function source()
    {
        return $this->belongsTo('WebModularity\LaravelLocal\Source');
    }

    public function getUrlAttribute() {
        return str_replace('{user_id}', $this->source_author_id, $this->source->url_review_user);
    }
}