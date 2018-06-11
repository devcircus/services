<?php

namespace BrightComponents\Service\Commands;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use BrightComponents\Service\Exceptions\InvalidNamespaceException;

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
     * The value of the command input after formatting.
     *
     * @var string
     */
    protected $inputValue;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (false === parent::handle() && ! $this->option('force')) {
            return;
        }

        if (! $this->option('self')) {
            $this->createHandler();
        }

        if (Config::get('servicehandler.cache')) {
            Cache::forget(Config::get('servicehandler.cache_key'));
        }
    }

    /**
     * Create a handler for the service.
     */
    protected function createHandler()
    {
        $this->call('make:handler', [
            'name' => $this->inputValue.Config::get('servicehandler.handler_suffix'),
        ]);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->option('self') ? __DIR__.'/stubs/service_self.stub' : __DIR__.'/stubs/service.stub';
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

        if ($this->option('self')) {
            if ($selfHandlingNamespace = Config::get('servicehandler.namespaces.self_handling')) {
                return $rootNamespace.'\\'.$serviceRootNamespace.'\\'.$selfHandlingNamespace;
            }

            return $rootNamespace.'\\'.$serviceRootNamespace;
        }

        if ($definitionNamespace = Config::get('servicehandler.namespaces.definitions')) {
            return $rootNamespace.'\\'.$serviceRootNamespace.'\\'.$definitionNamespace;
        }

        return $rootNamespace.'\\'.$serviceRootNamespace;
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $input = $input = studly_case(trim($this->argument('name')));
        $suffix = Config::get('servicehandler.definition_suffix');

        if (Config::get('servicehandler.override_duplicate_suffix')) {
            if ($suffix && ends_with($input, $suffix)) {
                $input = str_replace_last($suffix, '', $input);
            }
        }

        $this->inputValue = $input;

        return $input.$suffix;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['self', null, InputOption::VALUE_NONE, 'Indicates that service is self-handling.'],
        ];
    }
}
