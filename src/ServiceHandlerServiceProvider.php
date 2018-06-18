<?php

namespace BrightComponents\Service;

use BrightComponents\Service\Commands\HandlerMakeCommand;
use BrightComponents\Service\Commands\ServiceMakeCommand;
use BrightComponents\Service\Contracts\ServiceCallerContract;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use BrightComponents\Service\Contracts\ServiceTranslatorContract;

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
        $this->app->singleton(ServiceTranslator::class, function ($app) {
            return new ServiceTranslator(
                config('servicehandler.namespaces.root'),
                config('servicehandler.namespaces.definitions'),
                config('servicehandler.namespaces.handlers'),
                config('servicehandler.definition_suffix'),
                config('servicehandler.handler_suffix')
            );
        });

        $this->app->singleton(ServiceCaller::class, function ($app) {
            $translator = $app->make(ServiceTranslator::class);

            return new ServiceCaller($app, $translator);
        });

        $this->app->alias(
            ServiceCaller::class,
            ServiceCallerContract::class
        );

        $this->app->alias(
            ServiceTranslator::class,
            ServiceTranslatorContract::class
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
            HandlerMakeCommand::class,
        ]);

        $this->mapHandlers();
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
            ServiceTranslator::class,
            ServiceTranslatorContract::class,
        ];
    }

    /**
     * Map service handlers to services.
     *
     * @return \BrightComponents\Service\ServiceCaller
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
