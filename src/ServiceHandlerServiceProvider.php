<?php

namespace BrightComponents\Service;

use Illuminate\Support\Facades\Config;
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

        $this->app->singleton(ServiceTranslator::class, function ($app) {
            $translator = new ServiceTranslator();
            $translator::initialize($appNamespace = $app->getNamespace());

            return $translator;
        });

        $this->app->alias(
            ServiceCaller::class,
            ServiceCallerContract::class
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

        if (Config::get('servicehandler.autoload')) {
            $this->loadServices();
        }

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
            ServiceAutoloader::class,
            ServiceTranslator::class,
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
        $configHandlers = Config::get('servicehandler.handlers');
        if ($configHandlers && count($configHandlers)) {
            return $configHandlers;
        }

        return $this->handlers;
    }

    private function loadServices()
    {
        $this->handlers = $this->app->make(ServiceAutoloader::class)->load();
    }
}
