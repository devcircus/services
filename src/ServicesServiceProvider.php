<?php

namespace BrightComponents\Services;

use BrightComponents\Services\Commands\ServiceMakeCommand;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServicesServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton(ServiceCaller::class, function ($app) {
            return new ServiceCaller($app);
        });

        $this->app->alias(
            ServiceCaller::class,
            AbstractServiceCaller::class
        );
    }

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/service-classes.php' => config_path('service-classes.php'),
            ]);
        }

        $this->mergeConfigFrom(__DIR__.'/../config/service-classes.php', 'service-classes');

        $this->commands([
            ServiceMakeCommand::class,
        ]);

        ServiceCaller::setHandlerMethod(config('service-classes.method', 'run'));
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            ServiceCaller::class,
            AbstractServiceCaller::class,
        ];
    }
}
