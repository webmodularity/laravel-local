<?php

namespace WebModularity\LaravelLocal;

use Illuminate\Database\Eloquent\Model;
use WebModularity\LaravelLocal\ReviewAuthor;
use DB;

class Review extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['author_id', 'review_id', 'active', 'rating', 'review', 'is_excerpt', 'review_created_at', 'review_updated_at'];

    protected $dates = ['review_created_at', 'review_updated_at', 'created_at', 'updated_at'];

    /**
     * Get the ReviewAuthor that owns this Review.
     */
    public function author()
    {
        return $this->belongsTo('WebModularity\LaravelLocal\ReviewAuthor');
    }

    public function getUrlAttribute() {
        return str_replace('{review_id}', $this->review_id, $this->author->provider->url_review);
    }

    public static function createReviewFromImport($reviewAuthor, $review) {
        // Check for existence of this review_provider_author_id
        if (!ReviewAuthor::where('review_provider_author_id', $reviewAuthor['review_provider_author_id'])
            ->where('review_provider_id', $reviewAuthor['review_provider_id'])
            ->exists()) {
            DB::transaction(function () use ($reviewAuthor, $review) {
                $reviewAuthor = ReviewAuthor::create([
                    'review_provider_id' => $reviewAuthor['review_provider_id'],
                    'review_provider_author_id' => $reviewAuthor['review_provider_author_id'],
                    'name' => $reviewAuthor['name'],
                    'url_image' => $reviewAuthor['url_image']
                ]);

                Review::create([
                    'author_id' => $reviewAuthor->id,
                    'review_id' => $review['review_id'],
                    'active' => true,
                    'rating' => $review['rating'],
                    'review' => $review['review'],
                    'is_excerpt' => $review['is_excerpt'],
                    'review_created_at' => $review['review_created_at'],
                    'review_updated_at' => $review['review_updated_at']
                ]);
            });
            return 1;
        } else {
            // Review Already Exists
            return 0;
        }
    }

    public static function importFromGoogle($reviewProviderId, $data) {
        $createCount = 0;
        foreach ($data['result']['reviews'] as $googleReview) {
            if (isset($googleReview['author_url']) && preg_match('/\/(\d+)$/', $googleReview['author_url'], $idMatch)) {
                $reviewAuthor = [
                    'review_provider_id' => $reviewProviderId,
                    'review_provider_author_id' => $idMatch[1],
                    'name' => $googleReview['author_name'],
                    'url_image' => isset($googleReview['profile_photo_url']) ? $googleReview['profile_photo_url'] : null
                ];
                $review = [
                    // Currently no way to pull review ID from places API
                    'review_id' => null,
                    'rating' => $googleReview['rating'],
                    'review' => $googleReview['text'],
                    'is_excerpt' => false,
                    'review_created_at' => $googleReview['time'],
                    'review_updated_at' => $googleReview['time']
                ];
                $createCount += static::createReviewFromImport($reviewAuthor, $review);
            }
        }
        return $createCount;
    }

    public static function importFromYelp($reviewProviderId, $data) {
        $createCount = 0;
        foreach ($data['reviews'] as $yelpReview) {
            $urlImage = isset($yelpReview->user->image_url)
                ? preg_replace("/^http:/i", "https:", $yelpReview->user->image_url)
                : null;
            $reviewAuthor = [
                'review_provider_id' => $reviewProviderId,
                'review_provider_author_id' => $yelpReview->user->id,
                'name' => $yelpReview->user->name,
                'url_image' =>  $urlImage
            ];
            $review = [
                'review_id' => $yelpReview->id,
                'rating' => $yelpReview->rating,
                'review' => $yelpReview->excerpt,
                'is_excerpt' => true,
                'review_created_at' => $yelpReview->time_created,
                'review_updated_at' => $yelpReview->time_created
            ];
            $createCount += static::createReviewFromImport($reviewAuthor, $review);
        }
        return $createCount;
    }

}