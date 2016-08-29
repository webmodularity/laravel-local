<?php

namespace WebModularity\LaravelLocal;

use Illuminate\Support\ServiceProvider;

class LocalServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/local.php', 'local');
    }

    public function boot() {
        // Config
        $this->publishes([__DIR__ . '/config/local.php' => config_path('local.php')], 'config');
    }
}