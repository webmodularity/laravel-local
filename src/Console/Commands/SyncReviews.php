<?php

namespace WebModularity\LaravelLocal\Console\Commands;

use Illuminate\Console\Command;
use WebModularity\LaravelLocal\Review;
use WebModularity\LaravelProviders\ReviewProvider;
use WebModularity\LaravelProviders\Provider;
use Carbon\Carbon;

class SyncReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'local:sync-reviews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports reviews from available sources.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reviewsAdded = 0;
        $reviewSources = ReviewProvider::all()->keyBy('provider.slug');
        foreach (config('local.sources', []) as $sourceSlug => $sourceData) {
            if (in_array($sourceSlug, $reviewSources->keys()->toArray())
                && method_exists(Review::class, 'importFrom' . studly_case($sourceSlug))
                && method_exists(Provider::class, 'getDataFrom' . studly_case($sourceSlug))
            ) {
                $data = call_user_func([Provider::class, 'getDataFrom' . studly_case($sourceSlug)]);
                $reviewsAdded += call_user_func([Review::class, 'importFrom' . studly_case($sourceSlug)], $reviewSources[$sourceSlug]->id, $data);
            }
        }

        if ($reviewsAdded > 0) {
            $this->line(Carbon::now() . ' Reviews Added: ' . $reviewsAdded);
        }
    }
}
