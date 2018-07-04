<?php

namespace BrightComponents\Services\Traits;

use Illuminate\Container\Container;
use BrightComponents\Services\ServiceCaller;

trait CallsServices
{
    public function call($service)
    {
        return Container::getInstance()->make(ServiceCaller::class)->call($service);
    }
}
