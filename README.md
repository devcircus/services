# Bright Components - Services
### A Services implementation for Laravel Projects.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bright-components/services.svg)](https://packagist.org/packages/bright-components/services)
[![Build Status](https://img.shields.io/travis/bright-components/services/master.svg)](https://travis-ci.org/bright-components/services)
[![Quality Score](https://img.shields.io/scrutinizer/g/bright-components/services.svg)](https://scrutinizer-ci.com/g/bright-components/services)
[![Total Downloads](https://img.shields.io/packagist/dt/bright-components/services.svg)](https://packagist.org/packages/bright-components/services)

![Bright Components](https://s3.us-east-2.amazonaws.com/bright-components/bc_large.png "Bright Components")

### Disclaimer
The packages under the BrightComponents namespace are basically a way for me to avoid copy/pasting simple functionality that I like in all of my projects. There's nothing groundbreaking here, just a little extra functionality for form requests, controllers, custom rules, services, etc.

### Inspiration
BrightComponents' Service package scratches an itch I've had for a while. I routinely use single-action controllers with [Responder Classes](https://github.com/bright-components/responders), in combination with Service classes for gathering/manipulating data. In early iterations of this package, I modeled Service classes after Laravel's jobs. In doing so, I utilized a ServiceCaller that functioned like Laravel's Dispatcher. At this point, I've refactored to basically creating a class and calling a method. However, at this time, I'm keeping the ServiceCaller component, as I may need it in future iterations as I get closer to a 1.0 release.

Example:
```php
namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Services\StoreNewTaskService;
use App\Http\Requests\StoreNewTaskRequest;
use App\Http\Responders\Task\StoreResponder;
use BrightComponents\Services\Traits\CallsServices; // the trait could be added to your parent Controller class

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
        $task = $this->call(StoreNewTaskService::class, ($request->validated()));

        return $this->responder->respond($request, $task);
    }
}

```

My controllers simply defer to a Service to handle the dirty work, then, using [Responders](https://github.com/bright-components/responders), the response is sent. A very clean approach.

## Installation
You can install the package via composer. From your project directory, in your terminal, enter:
```bash
composer require bright-components/services
```
> Note: Until version 1.0 is released, major features and bug fixes may be added between minor versions. To maintain stability, I recommend a restraint in the form of "0.8.*". This would take the form of:
```bash
composer require "bright-components/services:0.8.*"
```

In Laravel > 5.6.0, the ServiceProvider will be automtically detected and registered.
If you are using an older version of Laravel, add the package service provider to your config/app.php file, in the 'providers' array:
```php
'providers' => [
    //...
    BrightComponents\Services\ServicesServiceProvider::class,
    //...
];
```

### Package Configuration
If you would like to change any of the package configuration options, run the following command in your terminal:
```bash
php artisan vendor:publish
```
and choose the 'BrightComponents/Services' option.

This will copy the package configuration (service-classes.php) to your 'config' folder.
See the configuration file below, for all options available:

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Namespaces
    |--------------------------------------------------------------------------
    |
    | Set the namespace for Service classes.
    |
    */
    'namespace' => 'Services',

    /*
    |--------------------------------------------------------------------------
    | Suffixes
    |--------------------------------------------------------------------------
    |
    | Set the suffix for the Service classes.
    |
    */
    'suffix' => 'Service',

    /*
    |--------------------------------------------------------------------------
    | Method Name
    |--------------------------------------------------------------------------
    |
    | Set the method name for handling services.
    |
     */
    'method' => 'run',

    /*
    |--------------------------------------------------------------------------
    | Duplicate Suffixes
    |--------------------------------------------------------------------------
    |
    | If you have a Service suffix set in the config and try to generate a Service that also includes the suffix,
    | the package will recognize this duplication and rename the Service to remove the extra suffix.
    | This is the default behavior. To override and allow the duplication, change to false.
    |
    */
    'override_duplicate_suffix' => true,

    'cached_services' => [
        /*
        |--------------------------------------------------------------------------
        | Namespaces
        |--------------------------------------------------------------------------
        |
        | Set the namespace for cached Service classes. The namespace is relative to the Services namespace defined above.
        |
        */
        'namespace' => 'Caching',

        /*
        |--------------------------------------------------------------------------
        | Namespaces
        |--------------------------------------------------------------------------
        |
        | Set the prefix for cached Service classes.
        |
        */
        'prefix' => 'Cached',

        /*
        |--------------------------------------------------------------------------
        | Parent class
        |--------------------------------------------------------------------------
        |
        | The shortname for the parent cached services class.
        |
        */
        'parent' => 'BaseCachedService',

        /*
        |--------------------------------------------------------------------------
        | Classes
        |--------------------------------------------------------------------------
        |
        | The mapping of cached services to their base service.
        |
        */
        'classes' => [],
    ],
];
```

## Usage
Once the package is installed and the config is copied (optionally), you can begin generating your Services.

### Generating a Service class
From inside your project directory, in your terminal, run:

```bash
php artisan adr:service StoreNewTask
```

Based on the configuration options above, this will create an 'App\Services\StoreNewTaskService' class.

> Note, by default, the 'run' method will be called when you 'call' your service. You can change this method name in the configuration file.

Example Service class:

```php
// Service Class
namespace App\Services;

use App\Models\Repositories\TaskRepository;
use BrightComponents\Common\Payloads\Payload;

class StoreNewTaskService
{
    /**
     * The parameters for building a new Task.
     *
     * @var array
     */
    public $repo;

    /**
     * Construct a new StoreNewTaskService.
     *
     * @param  \App\Models\Repositories\TaskRepository  $repo
     */
    public function __construct(TaskRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Handle the call to the service.
     *
     * @param  mixed  $params
     *
     * @return mixed
     */
    public function run($params)
    {
        $task = $this->repo->create($params);

        return new Payload(['task' => $task]);
    }
}
```
As in the example above, simply typehint any dependencies on the Service constructor. These dependencies will be resolved by Laravel from the container. Any parameters passed when calling the service, will be passed to the "run" method of the service.

> For consistency, you may choose to wrap your Service class returns in a Payload object. The Payload classes are included in the [bright-components/common package](https://github.com/bright-components/common). A Payload is a wrapper for the data being sent back to your controller. You can extend the AbstractPayload class, or use one of the generic Payload classes included(Payload and ErrorPayload). *These payload classes do not have any functionality at the moment. Future releases prior to 1.0 may introduce methods and/or properties for these classes.*

### Generating a Cached Service class
Cached services are a WIP. We'll update this place when complete. May generalize to a decorator service???

### How to call Services
At this time, there are several options for calling a service. Ideally, I'd like to only have a couple of options. As we near a 1.0 release, these options are likely to change.

First, you may use the included 'CallsServices' trait:
```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StoreNewTaskService;
use BrightComponents\Services\Traits\CallsServices;

class StoreTaskController extends Controller
{
    use CallsServices;

    public function store(Request $request)
    {
        $task = $this->call(StoreNewTaskService::class, ($request->all()));

        return view('tasks.show', ['task' => $task]);
    }
}
```

The next option is to include the ServiceCaller via dependency injection, the use the "call" method:
```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StoreNewTaskService;
use BrightComponents\Services\ServiceCaller;

class StoreTaskController extends Controllerr
{
    private $caller;

    public function __construct(ServiceCaller $caller)
    {
        $this->caller = $caller;
    }

    public function store(Request $request)
    {
        $task = $this->caller->call(StoreNewTaskService::class, ($request->all()));

        return view('tasks.show', ['task' => $task]);
    }
}
```

Next, the service has the ability to call itself:
```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StoreNewTaskService;

class StoreTaskController extends Controller
{
    public function store(Request $request)
    {
        $task = StoreNewTaskService::call($request->all());

        return view('tasks.show', ['task' => $task]);
    }
}
```

Last, and probably the most common way, is to inject the Service class in your Controller or Action, and call the 'run' method:
```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StoreNewTaskService;

class StoreTaskController extends Controllerr
{
    private $service;

    public function __construct(StoreNewTaskService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request)
    {
        $task = $this->service->run($request->all());

        return view('tasks.show', ['task' => $task]);
    }
}
```

### Note on Service class parameters
When calling your service class, you may pass multiple parameters:
```php
$this->call(MyService::class, $params, $anotherParam);
// or
$this->serviceCaller->call(MyService::class, $params, $anotherParam):
// or
MyService::call($params, $anotherParam);
// or
$service->run($params, $anotherParam);
```

I've found that usually, one array of parameters is sufficient, but you may have cases where you need to pass another parameter. Simply add these parameters when you call the Service, and these parameters will be passed to the 'run' method of your service. Be sure the 'run' method parameters match the arguments used, when the service is called:
```php
// MyServiceClass

public function run($data, $mystring, $anotherString)
{
    //
}

// In your controller
MyService::call($params, $string1, $string2);
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
