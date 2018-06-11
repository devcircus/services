<?php

namespace BrightComponents\Service\Commands;

use Illuminate\Support\Facades\Config;
use Illuminate\Console\GeneratorCommand;
use BrightComponents\Service\Exceptions\InvalidNamespaceException;

class HandlerMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:handler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service handler';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Handler';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/handler.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        $serviceRootNamespace = Config::get('servicehandler.namespaces.root');

        if (! $serviceRootNamespace) {
            throw InvalidNamespaceException::missingServiceRootNamespace();
        }

        $handlerNamespace = Config::get('servicehandler.namespaces.handlers');
        if ($handlerNamespace) {
            return $rootNamespace.'\\'.$serviceRootNamespace.'\\'.$handlerNamespace;
        }

        return $rootNamespace.'\\'.$serviceRootNamespace;
    }
}
