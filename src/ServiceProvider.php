<?php

namespace Zlj\Oa;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(Authorization::class, function () {
            return new Authorization(config('zljoa'));
        });

        $this->app->alias(Authorization::class, 'authorization');
    }

    public function provides()
    {
        return [Authorization::class, 'authorization'];
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/zljoa.php' => config_path('zljoa.php'),
        ], 'config');
    }
}