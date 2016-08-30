<?php

namespace WebModularity\LaravelLocal;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class LocalServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Console Commands
        $this->commands([
            Console\Commands\SyncReviews::class,
            Console\Commands\SyncHours::class,
        ]);
        // Schedule Sync Commands
        // Using scheduling commands via package from:
        // http://stackoverflow.com/questions/30456737/how-to-schedule-artisan-commands-in-a-package
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('local:sync-reviews')
                ->hourly()
                ->appendOutputTo(storage_path('logs/local.log'));
            $schedule->command('local:sync-hours')
                ->hourly()
                ->appendOutputTo(storage_path('logs/local.log'));
        });

        // Config
        $this->mergeConfigFrom(__DIR__ . '/../config/local.php', 'local');
    }

    public function boot() {
        // Migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Config
        $this->publishes([__DIR__ . '/../config/local.php' => config_path('local.php')], 'config');
    }
}