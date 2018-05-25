<?php

namespace BrightComponents\Service;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Contracts\Container\Container;

class ServiceCaller
{
    /**
     * The container implementation.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The pipeline instance for the bus.
     *
     * @var \Illuminate\Pipeline\Pipeline
     */
    protected $pipeline;

    /**
     * The pipes to send commands through before dispatching.
     *
     * @var array
     */
    protected $pipes = [];

    /**
     * The command to handler mapping for non-self-handling events.
     *
     * @var array
     */
    protected $handlers = [];

    /**
     * Create a new service caller instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->pipeline = new Pipeline($container);
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

        return $this->pipeline->send($service)->through($this->pipes)->then($callback);
    }

    /**
     * Determine if the given service has a defined stand-alone handler.
     *
     * @param  mixed  $service
     *
     * @return bool
     */
    public function hasServiceHandler($service)
    {
        return array_key_exists(get_class($service), $this->handlers);
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
        if ($this->hasServiceHandler($service)) {
            return $this->container->make($this->handlers[get_class($service)]);
        }

        return false;
    }

    /**
     * Set the pipes through which services should be piped before calling.
     *
     * @param  array  $pipes
     *
     * @return $this
     */
    public function pipeThrough(array $pipes)
    {
        $this->pipes = $pipes;

        return $this;
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
}
