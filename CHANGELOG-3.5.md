# Change for 3.5

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 3.5.3

Released: 2017-10-07

### Changes

* Load bootstrapper from `Orchestra\Testbench\Traits\CreateApplications::getPackageBootstrappers()` before bootstrapping service providers.

## 3.5.2

Released: 2017-09-28

### Changes

* Add mockery expectations to the assertion count. ([@scrubmx](https://github.com/scrubmx))

### Fixes

* Don't enable auto discovery for every package.

## 3.5.1

Released: 2017-09-05

### Changes

* Update Laravel skeleton and `Orchestra\Testbench\Exceptions\Handler`.
* Add `Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse` to `Orchestra\Testbench\Http\Kernel`. ([@kalfheim](https://github.com/kalfheim))
* Allow to use `Illuminate\Foundation\Testing\RefreshDatabase`. ([@BertvanHoekelen](https://github.com/BertvanHoekelen))

### Fixes

* Refresh named routes when declaring new routes from within a test method.

## 3.5.0

Released: 2017-08-30

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
