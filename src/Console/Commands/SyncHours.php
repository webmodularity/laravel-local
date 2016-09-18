<?php

namespace WebModularity\LaravelLocal\Console\Commands;

use Illuminate\Console\Command;
use WebModularity\LaravelLocal\Hour;
use WebModularity\LaravelProviders\Provider;
use Carbon\Carbon;

class SyncHours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'local:sync-hours';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports hours from Google source.';

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
        // Currently only supports Google for hours import
        $provider = Provider::where('slug', 'google')->first();
        if (method_exists(Hour::class, 'importFrom' . studly_case($provider->slug))
            && method_exists(Provider::class, 'getDataFrom' . studly_case($provider->slug))
        ) {
            $data = call_user_func([Provider::class, 'getDataFrom' . studly_case($provider->slug)]);
            $hoursChanged = call_user_func([Hour::class, 'importFrom' . studly_case($provider->slug)], $data);
        }

        if ($hoursChanged > 0) {
            $this->line(Carbon::now() . ' Hour Records Changed: ' . intval($hoursChanged));
        }
    }
}
