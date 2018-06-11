<?php

namespace BrightComponents\Service;

use Illuminate\Support\Facades\Config;
use BrightComponents\Service\Exceptions\InvalidSuffixException;
use BrightComponents\Service\Exceptions\MissingHandlerException;
use BrightComponents\Service\Exceptions\InvalidNamespaceException;

class ServiceTranslator
{
    /**
     * The FQCN of the Service Definition class.
     *
     * @var string
     */
    public $definitionClass;

    /**
     * The Application root namespace.
     *
     * @var string
     */
    public static $appNamespace;

    /**
     * The Service root namespace.
     *
     * @var string
     */
    public static $serviceNamespace;

    /**
     * The Definition namespace.
     *
     * @var string
     */
    public static $definitionNamespace;

    /**
     * The Handler namespace.
     *
     * @var string
     */
    public static $handlerNamespace;

    /**
     * The Definition suffix.
     *
     * @var string
     */
    public static $definitionSuffix;

    /**
     * The Handler suffix.
     *
     * @var string
     */
    public static $handlerSuffix;

    /**
     * The Definition directory.
     *
     * @var string
     */
    public static $definitions;

    /**
     * Initialize the Translator with Service properties.
     *
     * @param  string  $namespace
     */
    public static function initialize($namespace)
    {
        static::setAppNamespace($namespace);
        static::determineServiceProperties();
    }

    /**
     * Determine the Application root namespace.
     *
     * @return string
     */
    private static function setAppNamespace($namespace)
    {
        return self::$appNamespace = $namespace;
    }

    /**
     * Call each method that determines the static properties.
     */
    private static function determineServiceProperties()
    {
        static::determineServiceRootNamespace();
        static::determineDefinitionNamespace();
        static::determineDefinitionSuffix();
        static::determineHandlerNamespace();
        static::determineHandlerSuffix();
        static::determineDefinitionDirectory();
    }

    /**
     * Determine the Service root namespace.
     * ex. "App\Services".
     *
     * @throws \BrightComponents\Service\Exceptions\InvalidNamespaceException
     *
     * @return string
     */
    private static function determineServiceRootNamespace()
    {
        if (! $serviceNamespace = Config::get('servicehandler.namespaces.root')) {
            throw InvalidNamespaceException::missingServiceNamespace();
        }

        return self::$serviceNamespace = $serviceNamespace;
    }

    /**
     * Determine the full Definition namespace.
     * ex. "App\Services\Definitions".
     *
     * @return string
     */
    private static function determineDefinitionNamespace()
    {
        $definitionNamespace = Config::get('servicehandler.namespaces.definitions');

        return self::$definitionNamespace = $definitionNamespace
            ? self::$appNamespace.self::$serviceNamespace.'\\'.$definitionNamespace
            : self::$appNamespace.self::$serviceNamespace;
    }

    /**
     * Determine the full Handler namespace.
     * ex. "App\Services\Handlers".
     *
     * @return string
     */
    private static function determineHandlerNamespace()
    {
        $handlerNamespace = Config::get('servicehandler.namespaces.handlers');

        return self::$handlerNamespace = $handlerNamespace
            ? self::$appNamespace.self::$serviceNamespace.'\\'.$handlerNamespace
            : self::$appNamespace.self::$serviceNamespace;
    }

    /**
     * Determine the Definition suffix.
     * ex. "Definition".
     *
     * @throws \BrightComponents\Service\Exceptions\InvalidSuffixException
     *
     * @return string
     */
    private static function determineDefinitionSuffix()
    {
        if (! $suffix = Config::get('servicehandler.definition_suffix')) {
            throw InvalidSuffixException::missingServiceDefinitionSuffix();
        }

        return self::$definitionSuffix = $suffix;
    }

    /**
     * Determine the Handler suffix.
     * ex. "Handler".
     *
     * @return string
     */
    private static function determineHandlerSuffix()
    {
        return self::$handlerSuffix = Config::get('servicehandler.handler_suffix');
    }

    /**
     * Determine the Definition directory.
     *
     * @return string
     */
    private static function determineDefinitionDirectory()
    {
        return self::$definitions = __DIR__.'/../../../../'.str_replace('\\', '/', lcfirst(self::$definitionNamespace));
    }

    /**
     * Get the FQCN of the Service Definition.
     * ex. "App\Services\Definitions\CreatePostService".
     *
     * @param  \SplFileInfo  $file
     *
     * @return string
     */
    public function getDefinitionClass($file)
    {
        $relativePath = $this->getRelativeNamespace($file);
        $basename = explode('.', $file->getBasename())[0];

        return $relativePath ? $this::$definitionNamespace.'\\'.$relativePath.'\\'.$basename : $this::$definitionNamespace.'\\'.$basename;
    }

    /**
     * Get the FQCN of the Service Handler.
     * ex. "App\Services\Handlers\CreatePostHandler".
     *
     * @param  string  $definition
     *
     * @return string
     */
    public function getHandlerClass($definition)
    {
        $definitionBasename = $this->getDefinitionBasename($definition);
        $handlerBasename = str_replace_last($this::$definitionSuffix, $this::$handlerSuffix, $definitionBasename);
        $handler = str_replace_first($this::$definitionNamespace, $this::$handlerNamespace, $definition);
        $handler = str_replace_last($definitionBasename, $handlerBasename, $handler);

        if (! class_exists($handler)) {
            throw MissingHandlerException::unableToLocateHandler($handler);
        }

        return $handler;
    }

    /**
     * Get the Definition base name.
     * ex. "CreatePostService".
     *
     * @param  string  $definition  The FQCN of the service definition
     *
     * @return string
     */
    private function getDefinitionBasename($definition)
    {
        return class_basename($definition);
    }

    /**
     * Get the relative namespace.
     * ex. "Models\Poste".
     *
     * @param  \SplFileInfo  $file
     *
     * @return string
     */
    private function getRelativeNamespace($file)
    {
        return str_replace('/', '\\', $file->getRelativePath());
    }
}
