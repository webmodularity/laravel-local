<?php

use Illuminate\Database\Seeder;

class SourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('common.local_sources')->insert([
            [
                'name' => 'Google',
                'url' => 'https://www.google.com/search?q={id}',
                'url_review' => 'https://www.google.com/search?q={id}&ludocid={cid}#lrd=0x0:0x{lrd},1',
                'url_review_user' => 'https://www.google.com/maps/contrib/{user_id}/reviews'
            ],
            [
                'name' => 'Yelp',
                'url' => 'https://www.yelp.com/biz/{id}',
                'url_review' => 'https://www.yelp.com/biz/{id}?hrid={review_id}',
                'url_review_user' => 'https://www.yelp.com/user_details?userid={user_id}'
            ]
        ]);
    }
}
