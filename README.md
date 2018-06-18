# Bright Components - Service
### A "Definition/Handler" Implementation of Service classes for Laravel Projects.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bright-components/servicehandler.svg)](https://packagist.org/packages/bright-components/servicehandler)
[![Build Status](https://img.shields.io/travis/bright-components/servicehandler/master.svg)](https://travis-ci.org/bright-components/servicehandler)
[![Quality Score](https://img.shields.io/scrutinizer/g/bright-components/servicehandler.svg)](https://scrutinizer-ci.com/g/bright-components/servicehandler)
[![Total Downloads](https://img.shields.io/packagist/dt/bright-components/servicehandler.svg)](https://packagist.org/packages/bright-components/servicehandler)

![Bright Components](https://s3.us-east-2.amazonaws.com/bright-components/bc_large.png "Bright Components")

BrightComponents' Service package scratches an itch I've had for a while. I routinely use single-action controllers with [Responder Classes](https://github.com/bright-components/responders), in combination with Service classes for gathering/manipulating data. In the past, I used Laravel's jobs(synchronous) for my services. There were times, though, that I needed to use jobs as well and didn't like that they were difficult to differentiate from my Service classes. So, drawing heavily from Laravel's job dispatching, I made 'dispatchable' Services. In doing so, I can now go to my 'Services' folder and see a clear picture of all of my application services. There is a one-to-one mapping of Service Definitions and Service Handlers and my controllers are super clean!

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

My controllers simply defer to a Service to handle the dirty work, then, using [Responders](https://github.com/bright-components/responders), the response is sent. A very clean approach.

## Installation

You can install the package via composer:

```bash
composer require bright-components/servicehandler
```
> Note: Until version 1.0 is released, major features and bug fixes may be added between minor versions. To maintain stability, I recommend a restraint in the form of "^0.5.0". This would take the form of:
```bash
composer require "bright-components/servicehandler:^0.5.0"
```

In Laravel > 5.6.0, the ServiceProvider will be automatically detected and registered.

Then, if you would like to change any of the configuration options, run:
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
    | Namespaces
    |--------------------------------------------------------------------------
    |
    | Set the namespaces for the Service classes and Handlers.
    |
    */
    'namespaces' => [
        // The root namespace is in relation to the application root namespace, usually 'App'.
        'root' => 'Services',

        // The definitions and handlers namespace is in relation to the root Service namespace, listed above.
        'definitions' => 'Definitions',
        'handlers' => 'Handlers',

        // The self-handling services namespace is in relation to the root Service namespace, listed above.
        'self_handling' =>'',
    ],

    /*
    |--------------------------------------------------------------------------
    | Suffixes
    |--------------------------------------------------------------------------
    |
    | Set the suffix for the Service Definition and Handler class names. Use an empty string for no suffix.
    |
    | example: 'definition_suffix' => 'Service'
    |
    | NOTE: If you choose to store your definitions and handlers in the same namespace, you will need to provide a suffix
    | for, at least, either the definition or the handler. If not, the handler will not be created, due to the fact
    | that the make command will attempt to create two files in the same namespace with the same exact name.
    */
    'definition_suffix' => 'Service',
    'handler_suffix' => 'Handler',

    /*
    |--------------------------------------------------------------------------
    | Duplicate Suffixes
    |--------------------------------------------------------------------------
    |
    | If you have a definition suffix set in the config and try to generate a Service that also includes the suffix,
    | the package will recognize this duplication and rename the Service to remove the extra suffix.
    | This is the default behavior. To override and allow the duplication, change to false.
    |
    */
    'override_duplicate_suffix' => true,

    /*
    |--------------------------------------------------------------------------
    | Service / Handler mapping
    |--------------------------------------------------------------------------
    |
    | If you choose to utilize a namespace structure that can not be described by the configuration options above, you
    | can explicitly map your service definitions to handlers by providing the fully qualified namespace of each.
    | If there are name conflicts between services that have been explicitly mapped here and additional
    | services that have been defined in the application, the mapped handlers will take precedence.
    |
    */
    'handlers' => [
        // 'App\Services\Definitions\StoreItemService' => 'App\Services\Definitions\StoreItemServiceHandler',
    ],
];
```

## Usage
Once the package is installed and the config is copied (optionally), you can begin generating your Service Definitions and Handlers.
**To generate a Service Definition and Handler, run:**

```bash
php artisan make:service StoreNewTask
```

Based on the configuration options above, this will create a 'StoreNewTaskService' Definition class and a 'StoreNewTaskHandler' Handler class.

> Note, if you decide to use a namespace structure for your services, that can't be defined by the configuration options, you will need to explicitly define your service-to-handler mapping using the 'handlers' config option. See example below:
```php
    'handlers' => [
        'App\Services\Definitions\StoreNewTaskService' => 'App\Services\Handlers\StoreNewTaskHandler',
    ],
```
> Otherwise, you can use the default settings, or customize the namespaces and suffixes in the configuration. Based on these settings, the service will be translated to a handler at runtime. If your definitions and handlers are in the same namespace, you will need to assign a suffix to, at least, either your definitions or handlers. If not, the generator will attempt to create two classes with the same name in the same namespace. In this situation, your handler will not be generated.

**To generate a single, self-handling service with a "run" method, add the --self flag. Example:**
```bash
php artisan make:service StoreNewTask --self
```

This will generate one service class based on your namespace option in the servicehandler configuration. The "run" method on this class will be executed when you 'call' a service.

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
