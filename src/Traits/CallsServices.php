<?php

namespace BrightComponents\Services\Traits;

use Illuminate\Container\Container;
use BrightComponents\Services\ServiceCaller;

trait CallsServices
{
    /**
     * Call a service.
     *
     * @param  string  $service
     * @param  mixed  ...$params
     *
     * @return mixed
     */
    public function call(string $service, ...$params)
    {
        return Container::getInstance()->make(ServiceCaller::class)->call($service, ...$params);
    }
}
