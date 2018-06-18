<?php

namespace BrightComponents\Service\Contracts;

interface ServiceTranslatorContract
{
    /**
     * Translate the given service definition to the matching handler.
     *
     * @param  mixed  $service
     *
     * @return string
     */
    public function translateServiceToHandler($service): string;
}
