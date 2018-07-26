# Changelog

All notable changes to BrightComponents/Services will be documented in this file

## 0.1.0 - 2018-05-21

- initial release

## 0.2.0 - 2018-06-05

- Add autoloading of services
- Add definition and handler suffixes to configuration
- Add 'autoload' option to configuration
- Fix Handler naming issue when using make:service command

## 0.3.0 - 2018-06-05

- Add self-handling services

## 0.4.0 - 2018-06-11

- Add ability to cache the autoloaded service/handler mapping.
- Fixed generating and loading of services in nested namespaces.
- Removed helpers that had a dependency on Illuminate\Foundation.
- Rename 'service_suffix' to 'definition_suffix' for clarity.
- Refactored the Service Translator

## 0.4.1 - 2018-06-11

- Fix config only working in console.

## 0.5.0 - 2018-06-17

- Replace "autoloading" and cache features with runtime translating.
- Update dependencies and cleanup unused and unnecessary code.

## 0.5.1 - 2018-06-18

- Add docblocks to translator and interface.
- Fix comments in configuration to clarify parent namespace of self-handling services.

## 0.6.0 - 2018-07-01

- Replace the Service Definitions/Handler structure with a self-handling implementation.

## 0.6.1 - 2018-07-01

- Add a payload object for wrapping the result of the domain, to send to the responder.
- Add a trait to Services to allow a service to call itself. ie. MyService::call()

## 0.6.2 - 2018-07-02

- Fix Payload namespace.

## 0.6.3 - 2018-07-02

- Second attempt to fix Payload namespace.

## 0.6.4 - 2018-07-03

- Extract the Payload classes to bright-components/common package.
- Update README to reflect this change.

## 0.7.0 - 2018-07-04

- With Handler functionality removed, rename package to 'services' from 'servicehandler'.

## 0.7.1 - 2018-07-04

- Bump version to pull in master, including the new code coverage reporter id.

## 0.7.2 - 2018-07-08

- Rename make:service command namespace to "bright".

## 0.8.0 - 2018-07-10

- In Service classes, auto-resolved dependencies have been moved from the "run" method to the constructor. Parameters have been moved from the constructor to the "run" method.

### New Ways to Call Services
- Using the "CallsServices" trait:
```php
public function __invoke(Request $request)
{
    $result = $this->call(StoreCommentService::class, $request->all());
}
```

- Injecting the ServiceCaller and using the "call" method:
```php
public function __construct(ServiceCaller $caller)
{
    $this->caller = $caller;
}

public function __invoke(Request $request)
{
    $result = this->caller->call(StoreCommentService::class, $request->all());
}
```

- Using the "call" method of the Service class itself:
```php
public function __invoke(Request $request)
{
    $result = StoreCommentService::call($request->all());
}
```

## 0.8.1 - 2018-07-12

- Added initial tests for ServiceMakeCommand.
- Added ability to pass multiple parameters when calling a service.
- Update README.

## 0.8.2 - 2018-07-26

- With the new bright-components/adr package, we're changing the command namespace from 'bright' to 'adr'.