<?php

namespace BrightComponents\Service;

abstract class AbstractServiceCaller
{
    /**
     * The handler method to be called.
     *
     * @var string
     */
    public static $handlerMethod = 'run';

    /**
     * Call a service through its appropriate handler.
     *
     * @param  mixed  $service
     *
     * @return mixed
     */
    abstract public function call($service);

    /**
     * Determine if the given service has a handler.
     *
     * @param  mixed  $service
     *
     * @return bool
     */
    abstract public function hasHandler($service);

    /**
     * Set the handler method name for services.
     *
     * @param  string  $method
     */
    public static function setHandlerMethod(string $method = 'run')
    {
        static::$handlerMethod = $method;
    }
}
