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
    | Cache Service/Handler mapping
    |--------------------------------------------------------------------------
    |
    | If you are autoloading the services located in your chosen namespace, you can choose to cache the resulting mapping.
    | This allows your application to use the services without having to discover and parse services on each request.
    |
    */
    'cache' => false,
    'cache_key' => 'service_handlers',

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
    | NOTE: If you choose to autoload your service/handler mapping (option above), you MUST choose a suffix for your definition
    | classes. Otherwise, you will need to explicitly define your service/handler mapping using the 'handlers' array.
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
    | Map Handlers to Services
    |
    */
    'handlers' => [
        // 'App\Services\Definitions\StoreItemService' => 'App\Services\Definitions\StoreItemServiceHandler',
    ],
];
