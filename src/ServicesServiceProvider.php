<?php

namespace BrightComponents\Services;

use Illuminate\Support\Facades\Config;
use BrightComponents\Services\Commands\ServiceMakeCommand;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use BrightComponents\Services\Commands\CachedServiceMakeCommand;

class ServicesServiceProvider extends BaseServiceProvider
{
    /** @var array */
    protected $cachedServices = [];

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
            CachedServiceMakeCommand::class,
        ]);

        ServiceCaller::setHandlerMethod(config('service-classes.method', 'run'));

        if ('testing' != $this->app->environment()) {
            if (! file_exists(app_path().'/Providers/CachedServicesServiceProvider.php')) {
                $content = file_get_contents(__DIR__.'/stubs/cached-services-provider.stub');
                file_put_contents(app_path().'/Providers/CachedServicesServiceProvider.php', $content);
            }

            $this->app->register('App\Providers\CachedServicesServiceProvider');
        }
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
