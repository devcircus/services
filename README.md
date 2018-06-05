# Bright Components - Service
### A "Definition/Handler" Implementation of Service classes for Laravel Projects.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bright-components/servicehandler.svg)](https://packagist.org/packages/bright-components/servicehandler)
[![Build Status](https://img.shields.io/travis/bright-components/servicehandler/master.svg)](https://travis-ci.org/bright-components/servicehandler)
[![Quality Score](https://img.shields.io/scrutinizer/g/bright-components/servicehandler.svg)](https://scrutinizer-ci.com/g/bright-components/servicehandler)
[![Total Downloads](https://img.shields.io/packagist/dt/bright-components/servicehandler.svg)](https://packagist.org/packages/bright-components/servicehandler)

![Bright Components](https://s3.us-east-2.amazonaws.com/bright-components/bc_large.png "Bright Components")

BrightComponents' Service package scratches an itch I've had for a while. I routinely use single-action controllers with [Responder Classes](https://github.com/bright-components/responders), in combination with Service classes for gathering/manipulating data. In the past, I used Laravel's jobs(synchronous) for my services. There were times, though, that I needed to use jobs as well and didn't like that they were difficult to differentiate from my Service classes. So, drawing heavily from Laravel's job dispatching, I made 'dispatchable' Services. In doing so, I can now go to my 'Services' folder and see a clear picture of all of my application services. There is a one-to-one mapping of Service Definition and Service Handler and my controllers are super clean!

Example:
```php
namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewTaskRequest;
use App\Http\Responders\Task\StoreResponder;
use App\Services\Definitions\StoreNewTaskService;
use BrightComponents\Service\Traits\CallsServices; // the trait could be added to your parent Controller class

class Store extends Controller
{
    use CallsServices;

    /**
     * The service used to store a new task.
     *
     * @var \App\Http\Responders\Task\StoreResponder
     */
    private $responder;

    /**
     * Construct a new Store controller.
     *
     * @param  \App\Http\Responders\Task\StoreResponder  $responder
     */
    public function __construct(StoreResponder $responder)
    {
        $this->responder = $responder;
    }

    /**
     * Handle the task store action.
     *
     * @param  \App\Http\Requests\StoreTaskRequest  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(StoreTaskRequest $request)
    {
        $task = $this->call(new StoreNewTaskService($request->validated()));

        return $this->responder->respond($request, $task);
    }
}

```

My controllers simply defer to a Service to handle the dirty work, then, using [Responders](https://github.com/bright-components/responders), a responder is responsible for handling the response. A very clean approach.

## Installation

You can install the package via composer:

```bash
composer require bright-components/service
```

In Laravel > 5.6.0, the ServiceProvider will be automatically detected and registered.

Then, run:
```bash
php artisan vendor:publish
```
and choose the 'BrightComponents/Service' option.

This will copy the package configuration (servicehandler.php) to your 'config' folder.
See the configuration file below, for all options available:

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Autoload Services
    |--------------------------------------------------------------------------
    |
    | Autoload the services from the configured service namespace, instead of explicitly defining the mapping in
    | this configuration file or in the ServiceHandlerServiceProvider. This option is enabled by default.
    |
 */
    'autoload' => true,

    /*
    |--------------------------------------------------------------------------
    | Namespaces
    |--------------------------------------------------------------------------
    |
    | Set the namespaces for the Service classes and Handlers.
    |
    */

    'namespaces' => [
        'root' => 'Services',
        'definitions' => 'Definitions',
        'handlers' => 'Handlers',
    ],

    /*
    |--------------------------------------------------------------------------
    | Suffixes
    |--------------------------------------------------------------------------
    |
    | Set the suffix for the Service and Handler class names. Use an empty string for no suffix.
    |
    | example: 'service_suffix' => 'Service'
    |
    | When running 'php artisan make:service Testing', the above configuration will yield a service definition class
    | named 'TestingService'. However, if instead, your config looks like 'service_suffix' => '', the resulting
    | service definition class will simply be named 'Testing'. The handler suffix config works the same way.
    |
    | **Note**
    | If you incidentally run the command 'php artisan make:service TestingService' and you also have
    | 'Service' set as your suffix, the package will detect this and not duplicate the suffix.
    | You can override this behavior using the 'override_duplicate_suffix' config option.
    |
     */
    'service_suffix' => 'Service',
    'handler_suffix' => 'Handler',

    /*
    |--------------------------------------------------------------------------
    | Duplicate Suffixes
    |--------------------------------------------------------------------------
    |
    | If you have a suffix set in the config and try to generate a Service that also includes the suffix,
    | the package will recognize this duplication and rename the Service to remove the extra suffix.
    | This is the default behavior, to override and allow duplicate suffixes, change to false.
    |
     */
    'override_duplicate_suffix' => true,

    /*
    |--------------------------------------------------------------------------
    | Service / Handler mapping
    |--------------------------------------------------------------------------
    |
    | Map Handlers to Services. By default, there is no need to map your services to handlers. Your services will
    | be autoloaded from your services directory, according to the namespaces that have been provided above.
    | However, if you disable service autoloading, you will need to explicitly map your handlers here
    | or override the ServiceProvider and perform the service to handler mapping there instead.
    |
    */

    'handlers' => [
        // 'App\Services\Definitions\StoreNewTaskService' => 'App\Services\Definitions\StoreNewTaskHandler',
    ],
];
```

## Usage
Once the package is installed and the config is copied, you can begin generating your Service Definitions and Handlers.
To generate a Service Definition and Handler, run:

```bash
php artisan make:service StoreNewTask
```

Based on the configuration options above, this will create a 'StoreNewTaskService' Definition class and a 'StoreNewTaskHandler' Handler class.

> Note, if you decide not to use the 'autoload' functionality, in your config file, you can map the Service Definition to the Handler. See example below:
```php
    'handlers' => [
        'App\Services\Definitions\StoreNewTaskService' => 'App\Services\Handlers\StoreNewTaskHandler',
    ],
```
> If you prefer to autoload your services, be sure 'autoload' is set to true in the servicehandler configuration file. (This is the default)

Example Service Definition class:

```php
// Service Definition Class
namespace App\Services\Definitions;

class StoreNewTaskService
{
    /**
     * The parameters for building a new Task.
     *
     * @var array
     */
    public $params;

    /**
     * Construct a new StoreNewTaskService.
     *
     * @param  array  $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }
}
```

Now, you can call your service by using the included trait (CallsServices) or use dependency injection to add the ServiceCaller to your class:

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use BrightComponents\Service\ServiceCaller;
use App\Services\Definitions\StoreNewTaskService;
use BrightComponents\Service\Traits\CallsServices;

class StoreTaskController extends StoreTaskController
{
    // Use the provided trait
    use CallsServices;

    /* Or, alternatively, use dependency injection instead of the trait

    private $service;

    public function __construct(ServiceCaller $service)
    {
        $this->service = $service;
    }

    Then call the service using $this->service->call(new StoreNewTaskService());
    */

    public function store(Request $request)
    {
        $task = $this->call(new StoreNewTaskService($request->all()));

        return view('tasks.show', ['task' => $task]);
    }
}
```
As in the example above, simply pass any necessary data to your service definition constructor. The service and its associated data will be available in the 'run' method of the Handler. In your Handler class, you may typehint any dependencies needed in the class constructor and they will be resolved from the Container by Laravel.

```php
namespace App\Services\Handlers;

use App\Models\Task;
use App\Services\Definitions\StoreNewTaskService;

class StoreNewTaskHandler
{
    /**
     * The model.
     *
     * @var \App\Models\Task
     */
    public $model;

    /**
     * Construct anew StoreNewTaskHandler.
     *
     * @param  \App\Models\Task $task
     */
    public function __construct(Task $task)
    {
        $this->model = $task;
    }

    /**
     * Handle the storing of a new task.
     *
     * @param  \App\Services\Definition\StoreNewTaskService  $service
     *
     * @return \App\Models\Task
     */
    public function handle(StoreNewTaskService $service)
    {
        return $this->model->create($service->params);
    }
}
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email clay@phpstage.com instead of using the issue tracker.

## Roadmap

We plan to work on flexibility/configuration soon, as well as release a framework agnostic version of the package.

## Credits

- [Clayton Stone](https://github.com/devcircus)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
