<?php

namespace BrightComponents\Service;

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class ServiceAutoloader
{
    /**
     * The service definition to handler mapping.
     *
     * @var array
     */
    private $handlers = [];

    /**
     * The Service Translator class.
     *
     * @var \BrightComponents\Service\ServiceTranslator
     */
    protected $translator;

    /**
     * Construct a new ServiceAutoloader.
     *
     * @param  \BrightCompnonents\Service\ServiceTranslator  $translator
     */
    public function __construct(ServiceTranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Map services from the configured namespace to handlers and load into the handlers array.
     *
     * @return array
     */
    public function load()
    {
        if (File::exists($directory = $this->translator::$definitions)) {
            if (Config::get('servicehandler.cache')) {
                return Cache::rememberForever(Config::get('servicehandler.cache_key'), function () use ($directory) {
                    return $this->loadServicesFromDirectory($directory);
                });
            }

            return $this->loadServicesFromDirectory($directory);
        }

        return [];
    }

    /**
     * Cyle through the Service definitions directory, finding all Definition classes. Translate each Definition class
     * into a Handler class, then add the Definition => Handler to the handlers property of this Service Provider.
     *
     * @param  string  $directory
     *
     * @return array
     */
    protected function loadServicesFromDirectory($directory)
    {
        foreach (Finder::create()->in($directory)->name('*'.$this->translator::$definitionSuffix.'.php')->files() as $file) {
            $definition = $this->translator->getDefinitionClass($file);
            if (! method_exists($definition, 'run')) {
                $handler = $this->translator->getHandlerClass($definition);
                $this->associateHandlerWithDefinition($definition, $handler);
            }
        }

        return $this->handlers;
    }

    /**
     * Set the key/value pair in the handlers array.
     *
     * @param  string  $definitionPath
     * @param  string  $handlerPath
     *
     * @return string
     */
    protected function associateHandlerWithDefinition($definitionPath, $handlerPath)
    {
        return $this->handlers[$definitionPath] = $handlerPath;
    }
}
