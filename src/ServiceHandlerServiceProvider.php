<?php

namespace BrightComponents\Service;

use BrightComponents\Service\Commands\HandlerMakeCommand;
use BrightComponents\Service\Commands\ServiceMakeCommand;
use BrightComponents\Service\Contracts\ServiceCallerContract;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceHandlerServiceProvider extends BaseServiceProvider
{
    /**
     * Service to Handler mapping.
     *
     * @var array
     */
    protected $handlers = [];

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
            ServiceCallerContract::class
        );

        $this->mapHandlers();
    }

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/servicehandler.php' => config_path('servicehandler.php'),
        ]);

        $this->commands([
            ServiceMakeCommand::class,
            HandlerMakeCommand::class,
        ]);
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
            ServiceCallerContract::class,
        ];
    }

    /**
     * Map service handlers to services.
     *
     * @return \App\Foundation\Components\ServiceCaller
     */
    public function mapHandlers()
    {
        $this->app->make(ServiceCaller::class)->map($this->getHandlers());
    }

    /**
     * Get the service and handlers mapping.
     *
     * @return array
     */
    public function getHandlers()
    {
        $configHandlers = config('servicehandler.handlers');
        if ($configHandlers && count($configHandlers)) {
            return $configHandlers;
        }

        return $this->handlers;
    }
}
