# Change for 3.7

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 3.7.5

Released: 2018-10-07

### Changes 

* Allows `Orchestra\Testbench\Database\MigrateProcessor` to accept both `int` exit code and `Illuminate\Foundation\Testing\PendingCommand` when running migration via artisan.
* Update Laravel 5.7 skeleton.

## 3.7.4

Released: 2018-10-03

### Changes

* Update Laravel 5.7 skeleton.

## 3.7.3

Released: 2018-09-12

### Changes

* Always run artisan commands instead of getting an instance of `Illuminate\Foundation\Testing\PendingCommand`.

## 3.7.2

Released: 2018-09-12

### Changes

* Update Laravel 5.7 skeleton.
* Bump minimum Laravel Framework version to 5.7.4.

## 3.7.1

Released: 2018-09-07

### Changes

* Update Laravel 5.7 skeleton.

## 3.7.0

Released: 2018-09-04

### Added

* Added `Orchestra\Testbench\Http\Middleware\Authenticate` middleware.

### Changes

* Update support for Laravel Framework v5.7.
* `Orchestra\Testbench\Concerns\Testing::setUpTheTestEnvironmentTraits()` should always accept an array of `$uses` from `Orchestra\Testbench\TestCase::setUpTraits()`.
