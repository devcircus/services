<?php

namespace BrightComponents\Service\Traits;

use BrightComponents\Service\ServiceCaller;

trait SelfCallingService
{
    /**
     * Run the service.
     *
     * @return mixed
     */
    public static function call()
    {
        return app(ServiceCaller::class)->call(new static(...func_get_args()));
    }
}
