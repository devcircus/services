# Bright Components - Service
### A Services implementation for Laravel Projects.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bright-components/servicehandler.svg)](https://packagist.org/packages/bright-components/servicehandler)
[![Build Status](https://img.shields.io/travis/bright-components/servicehandler/master.svg)](https://travis-ci.org/bright-components/servicehandler)
[![Quality Score](https://img.shields.io/scrutinizer/g/bright-components/servicehandler.svg)](https://scrutinizer-ci.com/g/bright-components/servicehandler)
[![Total Downloads](https://img.shields.io/packagist/dt/bright-components/servicehandler.svg)](https://packagist.org/packages/bright-components/servicehandler)

![Bright Components](https://s3.us-east-2.amazonaws.com/bright-components/bc_large.png "Bright Components")

### Disclaimer
The packages under the BrightComponents namespace are basically a way for me to avoid copy/pasting simple functionality that I like in all of my projects. There's nothing groundbreaking here, just a little extra functionality for form requests, controllers, custom rules, services, etc.

BrightComponents' Service package scratches an itch I've had for a while. I routinely use single-action controllers with [Responder Classes](https://github.com/bright-components/responders), in combination with Service classes for gathering/manipulating data. In the past, I used Laravel's jobs(synchronous) for my services. There were times, though, that I needed to use jobs as well and didn't like that they were difficult to differentiate from my Service classes. Now, a quick look at my 'Services' folder and I can see a clear picture of all of my application services and my controllers are super clean!

Example:
```php
namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Services\StoreNewTaskService;
use App\Http\Requests\StoreNewTaskRequest;
use App\Http\Responders\Task\StoreResponder;
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
> Note: Until version 1.0 is released, major features and bug fixes may be added between minor versions. To maintain stability, I recommend a restraint in the form of "^0.6.0". This would take the form of:
```bash
composer require "bright-components/servicehandler:^0.6.0"
```

In Laravel > 5.6.0, the ServiceProvider will be automatically detected and registered.
If you are using an older version of Laravel, add the package service provider to your config/app.php file, in the 'providers' array:
```php
'providers' => [
    //...
    BrightComponents\Services\ServiceHandlerServiceProvider::class,
    //...
];
```

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
    | If you have a definition suffix set in the config and try to generate a Service that also includes the suffix,
    | the package will recognize this duplication and rename the Service to remove the extra suffix.
    | This is the default behavior. To override and allow the duplication, change to false.
    |
    */
    'override_duplicate_suffix' => true,
];
```

## Usage
Once the package is installed and the config is copied (optionally), you can begin generating your Services.
**To generate a Service class, run:**

```bash
php artisan make:service StoreNewTask
```

Based on the configuration options above, this will create an 'App\Services\StoreNewTaskService' class.

> Note, by default, the 'run' method will be called when you 'call' your service. You can change this method name in the configuration file.

Example Service Definition class:

```php
// Service Definition Class
namespace App\Services\Definitions;

use App\Models\Repositories\TaskRepository;
use BrightComponents\Service\Payloads\Payload;

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

    /**
     * Handle the call to the service.
     *
     * @return mixed
     */
    public function run(TaskRepository $repo)
    {
        $task = $repo->create($this->params);

        return new Payload(['task' => $task]);
    }
}
```
As in the example above, simply pass any necessary data to your service definition constructor. You may typehint any dependencies needed by your service in the 'run' method, and they will be resolved from the container by Laravel.
> Although the 'run' method may return data of any type you choose, I prefer wrapping the return data in a payload object. This way consistency is maintained between actions and a common format is forwarded to the responder. This opens the door to higher code integrity and clarity.

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
