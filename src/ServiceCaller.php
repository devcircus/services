<?php

namespace BrightComponents\Service;

use Illuminate\Container\Container;

class ServiceCaller
{
    /**
     * The container implementation.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The command to handler mapping for non-self-handling services.
     *
     * @var array
     */
    protected $handlers = [];

    /**
     * Create a new service caller instance.
     *
     * @param  \Illuminate\Container\Container  $container
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
        if ($handler = $this->getServiceHandler($service)) {
            $callback = function ($service) use ($handler) {
                return $handler->run($service);
            };
        } else {
            $callback = function ($service) {
                return $this->container->call([$service, 'run']);
            };
        }

        return call_user_func($callback, $service);
    }

    /**
     * Retrieve the handler for a service.
     *
     * @param  mixed  $service
     *
     * @return bool|mixed
     */
    public function getServiceHandler($service)
    {
        if ($this->hasHandler($service)) {
            return $this->container->make($this->handlers[get_class($service)]);
        }

        return false;
    }

    /**
     * Map a service to a handler.
     *
     * @param  array  $map
     *
     * @return $this
     */
    public function map(array $map)
    {
        $this->handlers = array_merge($this->handlers, $map);

        return $this;
    }

    /**
     * Determine if the given service has a handler defined.
     *
     * @param  mixed  $service
     *
     * @return bool
     */
    private function hasHandler($service)
    {
        return array_key_exists(get_class($service), $this->handlers);
    }
}
