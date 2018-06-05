<?php

namespace BrightComponents\Service;

use Illuminate\Console\DetectsApplicationNamespace;

class ServiceTranslator
{
    use DetectsApplicationNamespace;

    /**
     * Determine if a service can be translated to an existing handler.
     *
     * @param  mixed  $service
     *
     * @return bool|string
     */
    public function canTranslate($service)
    {
        return $this->hasHandler($service);
    }

    /**
     * Determine if a handler exists for a service.
     *
     * @param  mixed  $class
     *
     * @return bool|string
     */
    public function hasHandler($class)
    {
        $handler = $this->getHandler($class);

        return class_exists($handler) ? $handler : false;
    }

    /**
     * Get the handler for a specific service class.
     *
     * @param  mixed  $class
     *
     * @return string
     */
    protected function getHandler($class)
    {
        $definitionName = $this->parseDefintionClass($class);
        $handlerName = $this->convertDefinitionToHandler($definitionName);

        return $this->getHandlerPath($handlerName);
    }

    /**
     * Parse the definition class name from the fully qualified class name.
     *
     * @param  string  $class
     *
     * @return string
     */
    private function parseDefintionClass($class)
    {
        $classArray = explode('\\', get_class($class));

        return array_pop($classArray);
    }

    /**
     * Convert the definition class name to handler class name.
     *
     * @param  string  $definition
     *
     * @return string
     */
    public function convertDefinitionToHandler($definition)
    {
        $definition = class_basename($definition);
        $suffix = $this->getDefinitionSuffix();

        if ($suffix && ends_with($definition, $suffix)) {
            $definition = str_replace_last($suffix, '', $definition);
        }

        return $this->getAppNamespace()
            .config('servicehandler.namespaces.root', 'Services')
            .'\\'.config('servicehandler.namespaces.handlers', 'Handlers')
            .'\\'.str_finish($definition, $this->getHandlerSuffix());
    }

    /**
     * Get the namespace for service definitions.
     *
     * @return string
     */
    public function getServiceDefinitionsNamespace()
    {
        return $this->getAppNamespace().config('servicehandler.namespaces.root', 'Services').'\\'.config('servicehandler.namespaces.definitions', 'Definitions');
    }

    /**
     * Get the directory for service definitions.
     *
     * @return string
     */
    public function getServiceDefinitionsDirectory($namespace)
    {
        return __DIR__.'/../../../../'.str_replace('\\', '/', lcfirst($namespace));
    }

    /**
     * Determine if a suffix has been defined for service handlers.
     *
     * @return bool
     */
    private function hasHandlerSuffix()
    {
        return null != $this->getHandlerSuffix();
    }

    /**
     * Get the defined suffix for service handlers.
     *
     * @return string|null
     */
    private function getHandlerSuffix()
    {
        $configSuffix = config('servicehandler.handler_suffix');

        return trim(strlen($configSuffix)) > 0 ? $configSuffix : null;
    }

    /**
     * Determine if service handlers have a defined class name prefix.
     *
     * @return bool
     */
    private function hasDefinitionSuffix()
    {
        return null != $this->getDefinitionSuffix();
    }

    /**
     * Get the defined suffix for service definitions.
     *
     * @return string|null
     */
    private function getDefinitionSuffix()
    {
        return config('servicehandler.service_suffix');
    }
}
