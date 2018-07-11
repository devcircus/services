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
     * @param  mixed  $parameters
     *
     * @return mixed
     */
    public function call(string $service, $parameters)
    {
        return Container::getInstance()->make(ServiceCaller::class)->call($service, $parameters);
    }
}
