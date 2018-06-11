<?php

namespace BrightComponents\Service\Exceptions;

use Exception;

class InvalidNamespaceException extends Exception
{
    public static function missingServiceNamespace()
    {
        return new static('You must define a root namespace for your services.');
    }
}
