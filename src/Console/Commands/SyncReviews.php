<?php

namespace WebModularity\LaravelLocal\Console\Commands;

use Illuminate\Console\Command;
use WebModularity\LaravelLocal\Review;
use WebModularity\LaravelLocal\Source;
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
        $reviewSources = Source::whereNotNull('url_review')->get()->keyBy('name');
        foreach (config('local.sources', []) as $sourceName => $sourceData) {
            if (in_array($sourceName, $reviewSources->keys()->toArray())
                && method_exists(Review::class, 'importFrom' . $sourceName)
                && method_exists(Source::class, 'getDataFrom' . $sourceName)
            ) {
                $data = call_user_func([Source::class, 'getDataFrom' . $sourceName]);
                $reviewsAdded += call_user_func([Review::class, 'importFrom' . $sourceName], $reviewSources[$sourceName]->id, $data);
            }
        }

        if ($reviewsAdded > 0) {
            $this->line(Carbon::now() . 'Reviews Added: ' . $reviewsAdded);
        }
    }
}
