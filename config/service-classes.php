<?php

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
];
