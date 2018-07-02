<?php

namespace BrightComponents\Service\Traits;

trait SelfCallingService
{
    /**
     * Run the service.
     *
     * @return mixed
     */
    public static function run()
    {
        return app(static::class)->call(new static(...func_get_args()));
    }
}
