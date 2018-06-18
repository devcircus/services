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

    public function translateServiceToHandler($service) : string
    {
        $definitionClass = $this->getDefinitionClass($service);
        $rootNamespace = $this->getRootNamespace(get_class($service));
        $handlerClass = $this->convertDefinitionClassToHandlerClass($definitionClass);
        $fullNamespace = $this->getFullHandlerNamespace($rootNamespace);

        return $fullNamespace.'\\'.$handlerClass;
    }

    private function getDefinitionClass($service) : string
    {
        $classArray = explode('\\', get_class($service));

        return array_pop($classArray);
    }

    private function convertDefinitionClassToHandlerClass($definitionClass) : string
    {
        if ($definitionSuffix = $this->definitionSuffix) {
            return str_replace($definitionSuffix, $this->handlerSuffix, $definitionClass);
        }

        return $definitionClass.$this->handlerSuffix;
    }

    private function getRootNamespace($service) : string
    {
        $position = strpos($service, $this->serviceNamespace.'\\'.$this->definitionNamespace);
        if (false === $position) {
            return $service;
        } else {
            return substr($service, 0, $position);
        }
    }

    private function getFullHandlerNamespace($rootNamespace) : string
    {
        return $this->handlerNamespace ? $rootNamespace.$this->serviceNamespace.'\\'.$this->handlerNamespace : $rootNamespace.$this->serviceNamespace;
    }
}
