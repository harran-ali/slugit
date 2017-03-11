<?php

namespace Harran\Slugit;

use Illuminate\Support\ServiceProvider;
use Harran\Slugit\ModelObserver;
use Harran\Slugit\Services\SlugService;

class SlugitServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/slugit.php' => config_path('slugit.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/slugit.php', 'slugit');

        $this->app->bind(ModelObserver::class, function ($app) {
            return new ModelObserver(new SlugService);
        });
    }

}
