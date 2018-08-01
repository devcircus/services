<?php

namespace BrightComponents\Services\Traits;

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
        $decorator = resolve(static::class);

        return $decorator->cache(...$parameters);
    }
}
