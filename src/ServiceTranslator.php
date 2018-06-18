<?php

namespace BrightComponents\Service;

use BrightComponents\Service\Contracts\ServiceTranslatorContract;

class ServiceTranslator implements ServiceTranslatorContract
{
    /**
     * The root service namespace.
     *
     * @var string
     */
    private $serviceNamespace;

    /**
     * The definition namespace.
     *
     * @var string
     */
    private $definitionNamespace;

    /**
     * The handler namespace.
     *
     * @var string
     */
    private $handlerNamespace;

    /**
     * The suffix for definitions.
     *
     * @var string
     */
    private $definitionSuffix;

    /**
     * The suffix for handlers.
     *
     * @var string
     */
    private $handlerSuffix;

    /**
     * Construct a new ServiceTranslator.
     *
     * @param string $serviceNamespace
     * @param string $definitionNamespace
     * @param string $handlerNamespace
     * @param string $definitionSuffix
     * @param string $handlerSuffix
     */
    public function __construct(
        string $serviceNamespace,
        string $definitionNamespace,
        string $handlerNamespace,
        string $definitionSuffix,
        string $handlerSuffix
    ) {
        $this->serviceNamespace = $serviceNamespace;
        $this->definitionNamespace = $definitionNamespace;
        $this->handlerNamespace = $handlerNamespace;
        $this->definitionSuffix = $definitionSuffix;
        $this->handlerSuffix = $handlerSuffix;
    }

    /**
     * Translate the given service definition to the matching handler.
     *
     * @param  mixed  $service
     *
     * @return string
     */
    public function translateServiceToHandler($service): string
    {
        $definitionClass = $this->getDefinitionClass($service);
        $rootNamespace = $this->getRootNamespace(get_class($service));
        $handlerClass = $this->convertDefinitionClassToHandlerClass($definitionClass);
        $fullNamespace = $this->getFullHandlerNamespace($rootNamespace);

        return $fullNamespace.'\\'.$handlerClass;
    }

    /**
     * Get the definition class basename from the service definition class.
     *
     * @param  mixed  $service
     *
     * @return string
     */
    private function getDefinitionClass($service): string
    {
        $classArray = explode('\\', get_class($service));

        return array_pop($classArray);
    }

    /**
     * Convert the given definition class basename to the matching handler basename.
     *
     * @param  string  $definitionClass
     *
     * @return string
     */
    private function convertDefinitionClassToHandlerClass($definitionClass): string
    {
        if ($definitionSuffix = $this->definitionSuffix) {
            return str_replace($definitionSuffix, $this->handlerSuffix, $definitionClass);
        }

        return $definitionClass.$this->handlerSuffix;
    }

    /**
     * Get the root namespace for the application.
     *
     * @param  string  $service
     *
     * @return string
     */
    private function getRootNamespace($service): string
    {
        $position = strpos($service, $this->serviceNamespace.'\\'.$this->definitionNamespace);
        if (false === $position) {
            return $service;
        } else {
            return substr($service, 0, $position);
        }
    }

    /**
     * Get the full handler namespace.
     *
     * @param  string  $rootNamespace
     *
     * @return string
     */
    private function getFullHandlerNamespace($rootNamespace): string
    {
        return $this->handlerNamespace ? $rootNamespace.$this->serviceNamespace.'\\'.$this->handlerNamespace : $rootNamespace.$this->serviceNamespace;
    }
}
