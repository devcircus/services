<?php

namespace BrightComponents\Service\Contracts;

interface ServiceTranslatorContract
{
    public function translateServiceToHandler($service) : string;
}