<?php

namespace AvengersGroup;

use AvengersGroup\AbstractHandler;
use Illuminate\Support\ServiceProvider;

class MonitoringServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Abstract type to bind Monitoring Log Chatwork as in the Service Container.
     *
     * @var string
     */
    public static $abstract = 'monitoring';

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(static::$abstract, function ($app) {
            return new AbstractHandler($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [static::$abstract];
    }
}
