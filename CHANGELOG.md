# Changelog

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 3.5.0

Released: `<YYYY-MM-DD>`

### Added

* Add `getPackageBootstrappers()` method to load any bootstrap class specifically for a package.
* `Orchestra\Testbench\TestCase` now preloads following traits:
    - `Illuminate\Foundation\Testing\Concerns\InteractsWithSession`.
    - `Illuminate\Foundation\Testing\Concerns\InteractsWithExceptionHandling`.

### Changes

* Update support for Laravel Framework v5.5.
* Increase minimum PHP version to 7.0+ (tested with 7.0, 7.1 and 7.2).
* Increase minimum PHPUnit to v6.0+.
* Change skeleton folder from `fixture` to `laravel`.

### Fixes

* Refresh named routes when declaring new routes from within a test method.

### Removed

* Remove deprecated `Orchestra\Testbench\ApplicationTestCase`, use `Orchestra\Testbench\TestCase`.
* Remove deprecated `Orchestra\Testbench\Exceptions\ApplicationHandler`, use `Orchestra\Testbench\Exceptions\Handler`.
* Remove deprecated `Orchestra\Testbench\Traits\ApplicationTrait`, use `Orchestra\Testbench\Traits\CreatesApplication`.
* Remove depreacted `Orchestra\Testbench\TestCase::runLaravelDefaultMigrations()` method, use `loadLaravelMigrations()`.

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
