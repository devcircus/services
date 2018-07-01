<?php

namespace BrightComponents\Service\Exceptions;

use Exception;

class ServiceHandlerMethodException extends Exception
{
    public static function notFound($class)
    {
        return new static("Unable to locate handler for class, {$class}.");
    }
}
