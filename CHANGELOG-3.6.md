# Change for 3.6

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 3.6.7

Released: 2019-06-10

### Added

* Added `Orchestra\Testbench\Concerns\CreatesApplication::resetApplicationArtisanCommands()`.

### Changes

* Reset registered artisan commands when running migration from `Orchestra\Testbench\Concerns\WithLoadMigrationsFrom::loadMigrationsFrom()`.

## 3.6.6

Released: 2018-07-12

### Changes

* Update Laravel 5.6 skeleton.
* Avoid returning `self` unless the method is `final`.

## 3.6.5

Released: 2018-03-27

### Changes

* Bump minimum Laravel Framework `v5.6.13+` which support signed route URL.
* Update Laravel skeleton structure.

## 3.6.4

Released: 2018-03-13

### Added

* Added `Orchestra\Testbench\Concerns\WithFactories::loadFactoriesUsing()` helper to load factories before `$this->app` is available.

### Changes

* Allow `Orchestra\Testbench\Concerns\Testing::setUpTheTestEnvironmentTraits()` to access `$uses`.
* Update validation language file to include `not_regex`.

## 3.6.3

Released: 2018-02-20

### Fixes

* Fixes binding not overridden when trying to resolve providers and aliases.

## 3.6.2

Released: 2018-02-18

### Fixes

* Fixes invalid reference to `$overrides` on `Orchestra\Testbench\Concerns\CreatesApplication::resolveApplicationProviders()`.

## 3.6.1

Released: 2018-02-15

### Added

* Add `create-sqlite-db` command helper to assist using sqlite with Testbench.

### Changes

* Update `config/app.php` skeleton. ([@arcanedev-maroc](https://github.com/arcanedev-maroc))
* Allow to use `TestCase::loadLaravelMigrations()` without adding any parameters to use default database connection.

## 3.6.0

Released: 2018-02-08

### Added

* Added `Orchestra\Testbench\Database\MigrateProcessor`, to process realpath database migration instead of using `orchestra/database`.

### Changes

* Update support for Laravel Framework v5.6.
* Added `final`, scalar type-hint and scalar return type to internal methods, common extended methods remain the same for backward compatibilities.
