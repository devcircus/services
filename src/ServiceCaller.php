<?php

namespace BrightComponents\Service;

use Illuminate\Contracts\Container\Container;
use BrightComponents\Service\Exceptions\ServiceHandlerMethodException;

class ServiceCaller extends AbstractServiceCaller
{
    /**
     * The container implementation.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The handler method to be called.
     *
     * @var string
     */
    public static $handlerMethod;

    /**
     * Create a new service caller instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Call a service through its appropriate handler.
     *
     * @param  mixed  $service
     *
     * @return mixed
     */
    public function call($service)
    {
        if (! $this->hasHandler($service)) {
            throw ServiceHandlerMethodException::notFound($service);
        }

        return $this->container->call([$service, $this::$handlerMethod]);
    }

    /**
     * Determine if the service handler method exists.
     *
     * @param  mixed  $service
     *
     * @return bool
     */
    public function hasHandler($service)
    {
        return method_exists($service, $this::$handlerMethod);
    }
}
