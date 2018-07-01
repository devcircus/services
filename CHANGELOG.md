# Changelog

All notable changes to lara-service will be documented in this file

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