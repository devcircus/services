<?php

namespace BrightComponents\Service\Exceptions;

use Exception;

class MissingHandlerException extends Exception
{
    public static function unableToLocateHandler($class)
    {
        return new static("Unable to locate handler, {$class}.");
    }
}
