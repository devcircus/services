<?php

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
        // The root namespace is in relation to the application root namespace, usually 'App'.
        'root' => 'Services',

        // The definitions and handlers namespace is in relation to the root Service namespace, listed above.
        'definitions' => 'Definitions',
        'handlers' => 'Handlers',

        // The self-handling services namespace is a direct child of the application root namespace, usually 'App'.
        'self_handling' =>'Services',
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
    | Map Handlers to Services
    |
     */

    'handlers' => [
        // 'App\Services\Definitions\StoreItemService' => 'App\Services\Definitions\StoreItemServiceHandler',
    ],
];
