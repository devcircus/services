<?php

namespace BrightComponents\Service;

use BrightComponents\Service\Commands\ServiceMakeCommand;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceHandlerServiceProvider extends BaseServiceProvider
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
                __DIR__.'/../config/servicehandler.php' => config_path('servicehandler.php'),
            ]);
        }

        $this->mergeConfigFrom(__DIR__.'/../config/servicehandler.php', 'servicehandler');

        $this->commands([
            ServiceMakeCommand::class,
        ]);

        ServiceCaller::setHandlerMethod(config('servicehandler.method', 'run'));
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
