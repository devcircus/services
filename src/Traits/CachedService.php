<?php

namespace BrightComponents\Services\Traits;

use Illuminate\Support\Facades\Config;

trait CachedService
{
    /**
     * Respond to service call.
     *
     * @param  array  $parameters
     *
     * @return mixed
     */
    public static function call(...$parameters)
    {
        $decorator = new static(static::resolveBaseService());

        return $decorator->cache(...$parameters);
    }

    /**
     * Resolve the base service for the cached service.
     *
     * @return string
     */
    private static function resolveBaseService()
    {
        $cachedServices = Config::get('service-classes.cached_services.classes', null);

        $baseService = $cachedServices[static::class] ?? static::translateBaseService();

        return resolve($baseService);
    }

    /**
     * Translate the cached service name to the base service name.
     *
     * @return string
     */
    private static function translateBaseService()
    {
        $cachedServicePrefix = Config::get('service-classes.cached_services.prefix');
        $cachedServiceNamespace = '\\'.Config::get('service-classes.cached_services.namespace');

        return str_replace([$cachedServicePrefix, $cachedServiceNamespace], ['', ''], static::class);
    }
}
