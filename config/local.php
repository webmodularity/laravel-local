<?php

return [
    'providers' => [
        /**
         * To find Google values go to Google My Business and navigate to dashboard for this business
         * Navigate to the Info tab and find the "Published On" section
         * id: Click the Google Search and use the shortest value that pulls up business in google (try and crop address from search)
         * place_id: Go to https://developers.google.com/places/place-id and enter name of business then copy Place ID: (27 char string)
         * cid: Right click Google Maps and choose "Copy link location". Paste that into scratch file and extract cid=(19 digit number)
         * lrd: Go to http://www.uksbd.co.uk/local/cid-converter/ and enter this business' cid
         */
        'google' => [
            'id' => 'Google+ID',
            'place_id' => 'GOOGLE_PLACE_ID',
            'cid' => 'GOOGLE_CID',
            'lrd' => 'GOOGLE_LRD'
        ],
        /**
         * Go to yelp.com and search for business, ID is at end of URL yelp.com/biz/*yelp-id*
         * refer to http://www.yelp-support.com/article/What-is-my-Yelp-Business-ID?l=en_US for help
         */
        'yelp' => [
            'id' => 'yelp-id'
        ]
    ],
    /**
     * These API codes are from WebModularity and do not need to be altered
     */
    'api' => [
        'google' => [
            'key' => 'AIzaSyCu6nwXMMbXD7GItFIDNyZnFqR5ENnuj5c'
        ],
        'yelp' => [
            'consumerKey' => '5fA7XI6PXxTb06ZadqEJpA',
            'consumerSecret' => 'gO8kP7pnaZp5QAgjPZ9-mDjBFpg',
            'token' => 'kOb3JZIRGRnEMM7LZqtlRL53JC5EWKoa',
            'tokenSecret' => 'yzQgDkhxYz9t1kR44mEVYtR595o'
        ]
    ]
];