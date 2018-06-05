<?php

namespace BrightComponents\Service;

use Symfony\Component\Finder\Finder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\DetectsApplicationNamespace;

class ServiceAutoloader
{
    use DetectsApplicationNamespace;

    /**
     * The service definition to handler mapping.
     *
     * @var array
     */
    private $handlers = [];

    /**
     * The namespace for service definitions.
     *
     * @var string
     */
    private $namespace;

    /**
     * Construct a new ServiceAutoloader.
     *
     * @param  \Symfony\Component\Finder\Finder  $finder
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  \BrightCompnonents\Service\ServiceTranslator  $translator
     */
    public function __construct(Finder $finder, Filesystem $filesystem, ServiceTranslator $translator)
    {
        $this->finder = $finder;
        $this->filesystem = $filesystem;
        $this->translator = $translator;
    }

    /**
     * Load services from the service namespace into the handlers array.
     *
     * @return array
     */
    public function load()
    {
        $this->namespace = $this->getAppNamespace().config('servicehandler.namespaces.root', 'Services').'\\'.config('servicehandler.namespaces.definitions', 'Definitions');
        $directory = $this->translator->getServiceDefinitionsDirectory($this->namespace);

        if ($this->filesystem->exists($directory)) {
            $this->finder->files()->in($directory);

            if (count($this->finder)) {
                foreach ($this->finder as $file) {
                    $definition = $this->namespace.'\\'.$this->filesystem->name($file->getRealPath());
                    $handler = $this->translator->convertDefinitionToHandler($definition);
                    $this->associateHandlerWithDefinition($definition, $handler);
                }
            }
        }

        return $this->handlers;
    }

    private function associateHandlerWithDefinition($definitionPath, $handlerPath)
    {
        return $this->handlers[$definitionPath] = $handlerPath;
    }
}
