<?php

namespace WebModularity\LaravelLocal\Console\Commands;

use Illuminate\Console\Command;
use WebModularity\LaravelLocal\Hour;
use WebModularity\LaravelLocal\Source;
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
        $source = Source::where('name', 'Google')->first();
        if (method_exists(Hour::class, 'importFrom' . $source->name)
            && method_exists(Source::class, 'getDataFrom' . $source->name)
        ) {
            $data = call_user_func([Source::class, 'getDataFrom' . $source->name]);
            $hoursChanged = call_user_func([Hour::class, 'importFrom' . $source->name], $data);
        }

        //if ($hoursChanged > 0) {
            $this->line(Carbon::now() . ' Hour Records Changed: ' . intval($hoursChanged));
        //}
    }
}
