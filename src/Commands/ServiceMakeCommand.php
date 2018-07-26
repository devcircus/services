<?php

namespace BrightComponents\Services\Commands;

use Illuminate\Support\Facades\Config;
use Illuminate\Console\GeneratorCommand;
use BrightComponents\Services\Exceptions\InvalidNamespaceException;

class ServiceMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'adr:service {name}';

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
        $serviceRootNamespace = Config::get('service-classes.namespace');

        if (! $serviceRootNamespace) {
            throw InvalidNamespaceException::missingServiceNamespace();
        }

        return $rootNamespace.'\\'.$serviceRootNamespace;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     *
     * @return string
     */
    protected function getMethodName()
    {
        return Config::get('service-classes.method', 'run');
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $input = $input = studly_case(trim($this->argument('name')));
        $suffix = Config::get('service-classes.suffix');

        if (Config::get('service-classes.override_duplicate_suffix')) {
            return str_finish($input, $suffix);
        }

        return $input.$suffix;
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     *
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)->replaceMethod($stub)->replaceClass($stub, $name);
    }

    /**
     * Replace the method name in the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     *
     * @return $this
     */
    protected function replaceMethod(&$stub)
    {
        $stub = str_replace(
            ['DummyMethod'],
            [$this->getMethodName()],
            $stub
        );

        return $this;
    }
}
