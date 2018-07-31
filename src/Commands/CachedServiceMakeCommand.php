<?php

namespace BrightComponents\Services\Commands;

use Illuminate\Support\Facades\Config;
use Illuminate\Console\GeneratorCommand;
use BrightComponents\Services\Exceptions\InvalidNamespaceException;

class CachedServiceMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'adr:cache {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new cached service class.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Cached Service';

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

        return $namespace ? $rootNamespace.'\\'.Config::get('service-classes.namespace').'\\'.$namespace
                          : $rootNamespace.'\\'.Config::get('service-classes.cached_services.namespace');
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $input = $input = studly_case(trim($this->argument('name')));
        $prefix = Config::get('service-classes.cached_services.prefix');

        return str_start($input, $prefix);
    }
}
