<?php

namespace BrightComponents\Service\Contracts;

interface ServiceCallerContract
{
    /**
     * Call a service through its appropriate handler.
     *
     * @param  mixed  $service
     *
     * @return mixed
     */
    public function call($service);

    /**
     * Determine if the given service has a handler.
     *
     * @param  mixed  $service
     *
     * @return bool
     */
    public function hasServiceHandler($service);

    /**
     * Retrieve the handler for a service.
     *
     * @param  mixed  $service
     *
     * @return bool|mixed
     */
    public function getServiceHandler($service);

    /**
     * Set the pipes services should be piped through before calling.
     *
     * @param  array  $pipes
     *
     * @return $this
     */
    public function pipeThrough(array $pipes);

    /**
     * Map a service to a handler.
     *
     * @param  array  $map
     *
     * @return $this
     */
    public function map(array $map);
}
