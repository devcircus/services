<?php

namespace BrightComponents\Service\Exceptions;

use Exception;

class InvalidSuffixException extends Exception
{
    public static function missingServiceDefinitionSuffix()
    {
        return new static('You must define a suffix for your service definitions.');
    }
}
