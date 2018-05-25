<?php

namespace BrightComponents\Service\Commands;

use Illuminate\Console\GeneratorCommand;

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
        return $rootNamespace.'\\'.config('servicehandler.namespaces.root').'\\'.config('servicehandler.namespaces.handlers');
    }
}
