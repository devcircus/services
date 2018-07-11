<?php

namespace BrightComponents\Services\Tests\Integration;

use Orchestra\Testbench\TestCase as Orchestra;
use BrightComponents\Services\ServicesServiceProvider;

class IntegrationTestCase extends Orchestra
{
    /**
     * Setup the test case.
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Tear down the test case.
     */
    public function tearDown()
    {
        parent::tearDown();

        $file = app('Illuminate\Filesystem\Filesystem');
        $file->cleanDirectory(base_path().'/app/Services');
    }

    /**
     * Load ServiceProviders.
     */
    protected function getPackageProviders($app)
    {
        return [
            ServicesServiceProvider::class,
        ];
    }

    /**
     * Configure the environment.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app->setBasePath(__DIR__.'/../../vendor/orchestra/testbench-core/laravel/');

        // Configuration for Form Requests
        $app['config']->set('service-classes.namespace', 'Services');
        $app['config']->set('service-classes.suffix', 'Service');
        $app['config']->set('service-classes.method', 'run');
        $app['config']->set('service-classes.override_duplicate_suffix', true);
    }
}
