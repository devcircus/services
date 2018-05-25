<?php

namespace BrightComponents\Service;

use BrightComponents\Service\Traits\CallsServices;

abstract class Handler
{
    use CallsServices;

    /**
     * Handle the call to the service.
     *
     * @param  $service
     *
     * @return mixed
     */
    abstract public function run($service);
}
