# Change for 7.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 7.12.1

Released: 2022-11-12

### Fixes

* Fixes where the default database connection as `sqlite` causes an exception when the database file isn't available. The loaded application should revert to `testing` database connection for the state.

## 7.12.0

Released: 2022-11-12

### Added

* Added support for `about` artisan command.
* Added `package:devtool` to generate `.env`, `testbench.yaml` and `database.sqlite` file.
* Added `package:create-sqlite-db` and `package:drop-sqlite-db` command.
* Improves support for `serve` command.

## 7.11.2

Released: 2022-11-05

### Changes

* Improves `create-sqlite-db` and `drop-sqlite-db` command.
* Improves `Orchestra\Testbench\Foundation\Application` to allow uses `App\Http\Kernel` and `App\Console\Kernel` when available.

## 7.11.1

Released: 2022-11-05

### Changes

* Improves PHPStan support.

## 7.11.0

Released: 2022-10-19

### Added

* Added `Orchestra\Testbench\Foundation\Application::createVendorSymlink()` method.
  - The feature uses `Orchestra\Testbench\Foundation\Bootstrap\CreateVendorSymlink`.

### Changes

* Bump minimum `laravel/framework` to `9.36.0`
  - Forget View Component's cache and factory between tests.

## 7.10.2

Released: 2022-10-14

### Fixes

* Don't attempt to discover any packages on vendor symlink event.

## 7.10.1

Released: 2022-10-11

### Fixes

* Remove `bootstrap/cache/packages.php` on vendor symlink event.

## 7.10.0

Released: 2022-10-11

### Added

* Added `Orchestra\Testbench\Foundation\Config` to read Yaml file from `testbench.yaml`.

## 7.9.0

Released: 2022-10-05

### Added

* Added draft support for PHP 8.2.

### Changes

* Bump minimum `laravel/framework` to `9.34.0`.
* Bump minimum `mockery/mockery` to `1.5.1`.
* Bump minimum `symfony` dependencies to `6.0.9`.

## 7.8.1

Released: 2022-10-03

### Fixes

* Fixes missing `Illuminate\Support\Arr` import on `HandlesTestFailures` trait.

## 7.8.0

Released: 2022-09-28

### Changes

* Bump minimum `laravel/framework` to `9.32.0`.
* Improves PHPUnit memory leaks.

## 7.7.1

Released: 2022-09-28

### Changes

* Update skeleton to match v9.3.8.

## 7.7.0

Released: 2022-08-24

### Added

* Added `loadLaravelMigrationsWithoutRollback()` and `runLaravelMigrationsWithoutRollback()` helpers.

### Changes

* Update skeleton to match v9.3.5.

## 7.6.1

Released: 2022-08-10

### Changes

* Update skeleton to match v9.3.3.

## 7.6.0

Released: 2022-06-30

### Changes

* Bump minimum `laravel/framework` to `9.12.0`.
* Update skeleton to match v9.2.0.

## 7.5.0

Released: 2022-05-11

### Changes

* Bump minimum `laravel/framework` to `9.12.0`.
* Update skeleton to match v9.1.8.

## 7.4.0

Released: 2022-04-13

### Changes

* Bump minimum `laravel/framework` to `9.7.0`.
* Add support for `--drop-databases` on `package:test` command.
* Update skeleton to match v9.1.5.

## 7.3.0

Released: 2022-03-30

### Changes

* Bump minimum `laravel/framework` to `9.6.0`.
* Update skeleton to match v9.1.3.

## 7.2.0

Released: 2022-03-20

### Changes

* Bump minimum `laravel/framework` to `9.5.1`.
* Update skeleton to match v9.1.1.

## 7.1.0

Released: 2022-02-22

### Changes

* Bump minimum `laravel/framework` to `9.2`.
* Remove Laravel 9 beta compatibilities codes.

### Removed

* Remove `sanctum.php` configuration from skeleton. 

## 7.0.2

Released: 2022-02-16

### Changes

* Update skeleton to match v9.0.1.

## 7.0.1

Released: 2022-02-14

### Changes

* Add missing `lang/en.json` skeleton file.

## 7.0.0

Released: 2022-02-08

### Added

* Allows customizing default RateLimiter configuration via `resolveApplicationRateLimiting()` method.
* Added `Orchestra\Testbench\Http\Middleware\PreventRequestsDuringMaintenance` middleware.

### Changes

* Update support for Laravel Framework v9.
* Increase minimum PHP version to 8.0 and above (tested with 8.0 and 8.1).
* `$loadEnvironmentVariables` property is now set to `true` by default.
* Following internal classes has been marked as `final`:
    - `Orchestra\Testbench\Bootstrap\LoadConfiguration`
    - `Orchestra\Testbench\Console\Kernel`
    - `Orchestra\Testbench\Http\Kernel`
* Moved `resources/lang` skeleton files to `lang` directory.

### Removed

* Remove deprecated `Illuminate\Foundation\Testing\Concerns\MocksApplicationServices` trait.
