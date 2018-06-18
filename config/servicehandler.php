<?php

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
