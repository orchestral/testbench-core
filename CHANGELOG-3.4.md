# Changelog for 3.4

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 3.4.6

Released: 2017-02-20

### Fixes

* Fixes binding not overridden when trying to resolve providers and aliases.

## 3.4.5

Released: 2017-02-17

### Added

* Add `create-sqlite-db` command helper to assist using sqlite with Testbench.

### Changes

* Allow to use `TestCase::loadLaravelMigrations()` without adding any parameters to use default database connection.

### Fixes

* Fixes invalid reference to `$overrides` on `Orchestra\Testbench\Traits\CreatesApplication::resolveApplicationProviders()`.

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
