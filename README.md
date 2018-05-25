# Bright Components - Service
### A "Definition/Handler" Implementation of Service classes for Laravel Projects.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bright-components/servicehandler.svg)](https://packagist.org/packages/bright-components/servicehandler)
[![Build Status](https://img.shields.io/travis/bright-components/servicehandler/master.svg)](https://travis-ci.org/bright-components/servicehandler)
[![Quality Score](https://img.shields.io/scrutinizer/g/bright-components/servicehandler.svg)](https://scrutinizer-ci.com/g/bright-components/servicehandler)
[![Total Downloads](https://img.shields.io/packagist/dt/bright-components/servicehandler.svg)](https://packagist.org/packages/bright-components/servicehandler)

![Bright Components](https://s3.us-east-2.amazonaws.com/bright-components/bc_large.png "Bright Components")

## Installation

You can install the package via composer:

```bash
composer require bright-components/service
```

Then, run:
```bash
php artisan vendor:publish
```
and choose the BrightComponents/Service option.

This will copy the package configuration (servicehandler.php) to your 'config' folder.
Here, you can set your namespaces and also map your Service definitions to their appropriate handler class.

## Usage

BrightComponents/Service draws heavily from Laravel's Job dispatching component, therefore, the implementation and usage is very similar to dispatching jobs in Laravel. I simply wanted to be able to differentiate between 'jobs' and 'service' classes when perusing the codebase.

Once the package is installed, the config is copied, you can begin generating your Service classes and Handlers.
To generate a Service class and Handler, run:
```bash
php artisan make:service StoreNewTaskService
```
This will create a 'StoreNewTaskService' class and a 'StoreNewTaskServiceHandler' class according to the namespaces you set in the servicehandler.php config file.

Next, in the config file, you can map the Service to the Task. See example below:
```php
    'handlers' => [
        'App\Services\Definitions\StoreNewTaskService' => 'App\Services\Handlers\StoreNewTaskServiceHandler',
    ],
```
Alternatively, you could extend the base ServiceHandlerServiceProvider and using the 'getHandlers' method, return the mapping of Service Definitions to Service Handlers.

Now, you can call your service by using the included trait (CallsServices):
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use BrightComponents\Service\Traits\CallsServices;

class StoreTaskController extends StoreTaskController
{
    use CallsServices;

    public function store(Request $request)
    {
        $task = $this->call(new StoreNewTaskService($request->all()));

        return view('tasks.show', ['task' => $task]);
    }
}
```
As in the example above, simply pass any necessary data to your service definition constructor. The service and its associated data will be available in the 'run' method of the Handler. In your Handler class, you may typehint any dependencies needed in the class constructor and they will be resolved from the Container by Laravel.

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
