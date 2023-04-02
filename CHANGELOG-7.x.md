# Change for 7.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 7.24.1

Released: 2023-04-02

### Fixes

* Fixes `Orchestra\Testbench\Foundation\Config::addProviders()` usage.
* Fixes `Orchestra\Testbench\transform_relative_path()` logic.

## 7.24.0

Released: 2023-04-01

### Added

* Added `Orchestra\Testbench\Foundation\Bootstrap\LoadMigrationsFromArray` class to handle loading migrations from `testbench.yaml`.
    - You can now disable loading default migrations using either `migrations: false` in `testbench.yaml` or adding `TESTBENCH_WITHOUT_DEFAULT_MIGRATIONS=(true)` environment variable.
* Added additional configuration options to `testbench.yaml`:
    - `migrations: <bool|array>`
    - `bootstrappers: <array>`
* Added `Orchestra\Testbench\parse_environment_variables()` function.
* Added `Orchestra\Testbench\transform_relative_path()` function.

### Changes

* `env` configuration from `testbench.yaml` with have higher priority than `default_environment_variables()`.
* Disable `Dotenv\Repository\Adapter\PutenvAdapter` when generating environment variable on the fly using `Orchestra\Testbench\Foundation\Application`.

### Fixes

* Fixes console output when an exception is thrown before application can be bootstrapped.
* Fixes some configuration value leaks between tests due to the way it set environment values including `APP_KEY`, `APP_DEBUG` etc.

## 7.23.0

Released: 2023-03-27

### Added

* Add supports for `setup<Concern>` and `teardown<Concern>` with imported traits.

## 7.22.2

Released: 2023-03-23

### Fixes

* Avoid database connection from eager loaded via `spatie/laravel-ray`.

## 7.22.1

Released: 2023-02-03

### Changes

* Bump minimum `laravel/framework` to `9.52.4`.

## 7.22.0

Released: 2023-02-08

### Changes

* Improve `package:test --parallel` command usage.
* Use `$app->bootstrapPath()` instead of `$app->basePath('bootstrap')` whenever possible.

## 7.21.0

Released: 2023-02-03

### Added

* Added support for `Illuminate\Foundation\Testing\DatabaseTruncation`.

### Changes

* Bump minimum `laravel/framework` to `9.50.2`.

## 7.20.0

Released: 2023-02-01

### Changes

* Improves `package:test` commands.
* Update skeleton to match v9.5.2.

## 7.19.0

Released: 2023-01-10

### Added

* Added `Illuminate\Foundation\Testing\InteractsWithDeprecationHandling` to `Orchestra\Testbench\TestCase`.

## 7.18.0

Released: 2023-01-03

### Added

* Added `Orchestra\Testbench\laravel_version_compare` function as alias to `version_compare` specifically for Laravel Framework.
* Added `Orchestra\Testbench\phpunit_version_compare` function as alias to `version_compare` specifically for PHPUnit.
* Added `Orchestra\Testbench\Exceptions\PHPUnitErrorException` class.

### Changes

* Mark `Orchestra\Testbench\Bootstrap\ConfigureRay` class as `final`.
* Refactor `Orchestra\Testbench\Concerns\HandlesAnnotations` trait.

## 7.17.0

Released: 2022-12-22

### Changes

* Bump minimum `laravel/framework` to `9.45.0`.
* Update skeleton to match v9.4.1.

## 7.16.0

Released: 2022-12-17

### Added

* Added `resolveApplicationEnvironmentVariables()` method.
* Added `Orchestra\Testbench\Bootstrap\HandleExceptions` bootstrap to allow catching deprecation errors during tests.
  - Throws `Orchestra\Testbench\Exceptions\DeprecatedException` exception when deprecation occured.
  - Set `logging.deprecations.trace` to `true`.
  - Set deprecations log file to `storage/logs/deprecations.log` when `LOG_DEPRECATIONS_CHANNEL=single`.

### Changes

* Bump minimum `laravel/framework` to `9.44.0`.

## 7.15.0

Released: 2022-11-30

### Changes

* Bump minimum `laravel/framework` to `9.41.0`.

## 7.14.1

Released: 2022-11-29

### Fixes

* Fixes `serve` command with `no-reload` options.

## 7.14.0

Released: 2022-11-22

### Added

* Added `Orchestra\Testbench\Exceptions\ApplicationNotAvailableException` exception when trying to access `$this->app` outside of booted application.
* Added `tests/CreatesApplication.php` to skeleton.

### Changes

* Update skeleton to match v9.3.11.

## 7.13.0

Released: 2022-11-14

### Added

* Added `Orchestra\Testbench\Bootstrap\ConfigureRay` and use it when creating Application.

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
