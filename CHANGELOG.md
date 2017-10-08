# Changelog

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 3.4.4

Released: 2017-10-08

### Changes

* Revert loading custom bootstrapper before bootstrapping service provider. This is to allow configuration properly be configured from service provider before initiating custom bootstrapper.

## 3.4.3

Released: 2017-10-07

### Added

* Add `Orchestra\Testbench\Traits\CreateApplications::getPackageBootstrappers()` and refactor loading methods to `Orchestra\Testbench\Traits\CreateApplications::resolveApplicationBootstrappers()`.

## 3.4.2

Released: 2017-09-19

### Fixes

* Refresh named routes when declaring new routes from within a test method.

## 3.4.1

Released: 2017-08-19

### Added

* Add `Orchestra\Testbench\Traits\CreateApplication::overrideApplicationBindings()`.
* Add `Orchestra\Testbench\Traits\CreateApplication::overrideApplicationAliases()`.
* Add `Orchestra\Testbench\Traits\CreateApplication::overrideApplicationProviders()`
* Add `sqlsrv` database configuration template.

## 3.4.0

Released: 2017-06-07

### Added

* Move code from `orchestra/testbench` repository.

### Changes

* Update support for Laravel Framework v5.4.
