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
    protected $description = 'Imports reviews from available providers.';

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
        $reviewProviders = ReviewProvider::all()->keyBy('provider.slug');
        foreach (Provider::getConfiguredProviderSlugs() as $providerSlug) {
            $studlyCaseSlug = studly_case($providerSlug);
            if (in_array($providerSlug, $reviewProviders->keys()->toArray())
                && method_exists(Review::class, 'importFrom' . $studlyCaseSlug)
                && method_exists(Provider::class, 'getDataFrom' . $studlyCaseSlug)
            ) {
                $data = call_user_func([Provider::class, 'getDataFrom' . $studlyCaseSlug]);
                $reviewsAdded += call_user_func([Review::class, 'importFrom' . $studlyCaseSlug], $reviewProviders[$providerSlug]->id, $data);
            }
        }

        if ($reviewsAdded > 0) {
            $this->line(Carbon::now() . ' Reviews Added: ' . $reviewsAdded);
        }
    }
}
