<?php

namespace BrightComponents\Services\Commands;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\GeneratorCommand;
use BrightComponents\Services\Exceptions\InvalidNamespaceException;

class CachedServiceMakeCommand extends GeneratorCommand
{
    /** @var string */
    protected $signature = 'adr:cache {name} {--parent} {--generate-parent}';

    /** @var string */
    protected $description = 'Create new cached service class.';

    /** @var string */
    protected $type = 'Cached Service';

    /** @var string */
    protected $defaultNamespace;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (false === parent::handle() && ! $this->option('force')) {
            return;
        }

        if ($this->option('parent')) {
            Artisan::call('adr:cache', [
                'name' => 'BaseCachedService',
                '--generate-parent' => true,
            ]);
        }
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('generate-parent')) {
            return __DIR__.'/stubs/cached-service-parent.stub';
        }

        if ($this->option('parent')) {
            return __DIR__.'/stubs/cached-service-child.stub';
        }

        return __DIR__.'/stubs/cached-service.stub';
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
        $namespace = Config::get('service-classes.cached_services.namespace');
        $serviceNamespace = Config::get('service-classes.namespace');

        if (! $serviceNamespace) {
            throw InvalidNamespaceException::missingServiceNamespace();
        }

        return $this->defaultNamespace = $namespace ? $rootNamespace.'\\'.Config::get('service-classes.namespace').'\\'.$namespace
                      : $rootNamespace.'\\'.Config::get('service-classes.cached_services.namespace');
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return studly_case(trim($this->argument('name')));
    }

    /**
     * Get the parent name for the child class.
     *
     * @param  string  $rootNamespace
     *
     * @return string
     */
    protected function getParentName()
    {
        return Config::get('service-classes.cached_services.parent');
    }

    /**
     * Get the parent fqcn for the child class.
     *
     * @param  string  $rootNamespace
     *
     * @return string
     */
    protected function getParentFqcn()
    {
        return $this->defaultNamespace.'\\'.Config::get('service-classes.cached_services.parent');
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

        return $this->option('parent')
                        ? $this->replaceNamespace($stub, $name)->replaceParentClass($stub)->replaceParentFqcn($stub)->replaceClass($stub, $name)
                        : $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    /**
     * Replace the parent class in the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     *
     * @return $this
     */
    protected function replaceParentClass(&$stub)
    {
        $stub = str_replace('{DummyParent}', $this->getParentName(), $stub);

        return $this;
    }

    /**
     * Replace the parent fqcn in the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     *
     * @return $this
     */
    protected function replaceParentFqcn(&$stub)
    {
        $stub = str_replace('{DummyParentFqcn}', $this->getParentFqcn(), $stub);

        return $this;
    }
}
