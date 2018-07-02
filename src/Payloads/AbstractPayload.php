<?php

namespace BrightComponents\Services\Payloads;

abstract class AbstractPayload
{
    /**
     * The payload data.
     *
     * @var mixed|null
     */
    protected $data = null;

    /**
     * Construct a new Payload class.
     *
     * @param  mixed|null
     */
    public function __construct($data)
    {
        $this->setData($data);
    }

    /**
     * Set the response data.
     *
     * @return mixed|null
     */
    public function setData($data = null)
    {
        return tap($this, function ($payload) use ($data) {
            if (is_array($data)) {
                return $payload->data = $data;
            }
            if ($data instanceof \Illuminate\Support\Collection) {
                return $payload->data = $data->all();
            }

            return $payload->data = $data;
        });
    }

    /**
     * Get the response data.
     *
     * @return mixed|null
     */
    public function getData()
    {
        return $this->data;
    }
}
