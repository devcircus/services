<?php

namespace BrightComponents\Services\Tests\Integration;

trait TestsCommands
{
    public function assertMethodExists(string $className, string $methodName)
    {
        $this->assertTrue(method_exists($className, $methodName));
    }
}
