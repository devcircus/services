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
namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemRequest;
use App\Http\Responders\Item\StoreResponder;
use App\Services\Definitions\StoreItemService;
use BrightComponents\Service\Traits\CallsServices; // the trait could be added to your parent Controller class

class Store extends Controller
{
    use CallsServices;

    /**
     * The service used to store a new item.
     *
     * @var \App\Http\Responders\Items\StoreResponder
     */
    private $responder;

    /**
     * Construct a new Store controller.
     *
     * @param  \App\Http\Responders\Item\StoreResponder  $responder
     */
    public function __construct(StoreResponder $responder)
    {
        $this->responder = $responder;
    }

    /**
     * Handle the Item store action.
     *
     * @param  \App\Http\Requests\StoreItemRequest  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(StoreItemRequest $request)
    {
        $item = $this->call(new StoreItemService($request->validated()));

        return $this->responder->respond($request, $item);
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
Here, you can set your Service Definition and Handler namespaces and also map your Service definitions to their appropriate handler class:

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
        'root' => 'Services',
        'definitions' => 'Definitions',
        'handlers' => 'Handlers',
    ],

    /*
    |--------------------------------------------------------------------------
    | Service / Handler mapping
    |--------------------------------------------------------------------------
    |
    | Map Handlers to Services
    |
    */

    'handlers' => [
        'App\Services\Definitions\StoreItemService' => 'App\Services\Definitions\StoreItemServiceHandler',
    ],
];
```

> Note: Currently, you have to map your services/handlers in the provided config, or override the service provider and call the ```getHandlers()``` method. Using that method, you return the mapping array however you would like. In the near future, the package will assume a naming convention and do this automatically, leaving you the option to handle the mapping manually if you wish.

## Usage
Once the package is installed and the config is copied, you can begin generating your Service Definitions and Handlers.
To generate a Service Definition and Handler, run:

```bash
php artisan make:service StoreNewTaskService
```

This will create a 'StoreNewTaskService' Definition class and a 'StoreNewTaskServiceHandler' Handler class according to the namespaces you set in the servicehandler.php config file.

Next, in the config file, you can map the Service to the Task. See example below:
```php
    'handlers' => [
        'App\Services\Definitions\StoreNewTaskService' => 'App\Services\Handlers\StoreNewTaskServiceHandler',
    ],
```

As mentioned, alternatively, you could extend the base ServiceHandlerServiceProvider and using the ```getHandlers()``` method, return the mapping of Service Definitions to Service Handlers.

Example Service Definition class:

```php
// Service Definition Class
namespace App\Services\Definitions;

class StoreItemService
{
    /**
     * The parameters for building a new Item.
     *
     * @var array
     */
    public $params;

    /**
     * Construct a new StoreItemService.
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

use App\Models\Item;
use App\Services\Definitions\StoreItemService;

class StoreItemServiceHandler
{
    /**
     * The model.
     *
     * @var \App\Models\Item
     */
    public $model;

    /**
     * Construct anew StoreItemServiceHandler.
     *
     * @param  \App\Models\Item $item
     */
    public function __construct(Item $item)
    {
        $this->model = $item;
    }

    /**
     * Handle the storing of a new item.
     *
     * @param  \App\Services\Definition\StoreItemService  $service
     *
     * @return \App\Models\Item
     */
    public function handle(StoreItemService $service)
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
