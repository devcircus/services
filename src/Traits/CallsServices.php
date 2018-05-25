<?php

namespace BrightComponents\Service\Traits;

use Illuminate\Container\Container;
use BrightComponents\Service\ServiceCaller;

trait CallsServices
{
    public function call($service)
    {
        return Container::getInstance()->make(ServiceCaller::class)->call($service);
    }
}
