<?php

namespace WebModularity\LaravelLocal;

use SKAgarwal\GoogleApi\PlacesApi;
use SKAgarwal\GoogleApi\Exceptions\GooglePlacesApiException;
use Stevenmaguire\Yelp\Client as YelpClient;
use Log;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'common.local_sources';

    public function getUrlAttribute($value) {
        return static::urlReplace($this->name, $value);
    }

    public function getUrlReviewAttribute($value) {
        return static::urlReplace($this->name, $value);
    }

    public function getUrlReviewUserAttribute($value) {
        return static::urlReplace($this->name, $value);
    }

    public static function getUrlReplaceConfig($sourceName) {
        $configData = config('local.sources.' . $sourceName, []);
        $parsed = [];
        foreach ($configData as $key => $data) {
            $parsed['{' . $key . '}'] = $data;
        }
        return $parsed;
    }

    public static function urlReplace($sourceName, $url) {
        return str_replace(array_keys(static::getUrlReplaceConfig($sourceName)), static::getUrlReplaceConfig($sourceName), $url);
    }

    public static function getDataFromGoogle() {
        $apiKey = config('local.api.Google.key');
        $placeId = config('local.sources.Google.place_id');
        $googlePlaces = new PlacesApi($apiKey);
        $response = null;
        try {
            $response = $googlePlaces->placeDetails($placeId);
        } catch (GooglePlacesApiException $e) {
            Log::critical('GooglePlaces: ' . $e->getMessage(), [
                'api_key' => $apiKey,
                'place_id' => $placeId
            ]);
        }
        return $response;
    }

    public static function getDataFromYelp() {
        $client = new YelpClient([
            'consumerKey' => config('local.api.Yelp.consumerKey'),
            'consumerSecret' => config('local.api.Yelp.consumerSecret'),
            'token' => config('local.api.Yelp.token'),
            'tokenSecret' => config('local.api.Yelp.tokenSecret')
        ]);
        return collect($client->getBusiness(config('local.sources.Yelp.id')));
    }
}
