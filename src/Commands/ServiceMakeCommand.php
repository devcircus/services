<?php

namespace BrightComponents\Service\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

class ServiceMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new service definition and handler classes';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Service';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (false === parent::handle() && ! $this->option('force')) {
            return;
        }

        $this->createHandler();
    }

    /**
     * Create a handler for the service.
     */
    protected function createHandler()
    {
        $handler = Str::studly(class_basename($this->argument('name')));

        $this->call('make:handler', [
            'name' => $handler.config('servicehandler.handler_suffix', ''),
        ]);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/service.stub';
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
        return $rootNamespace.'\\'.config('servicehandler.namespaces.root').'\\'.config('servicehandler.namespaces.definitions');
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return trim($this->argument('name')).config('servicehandler.service_suffix', '');
    }
}
