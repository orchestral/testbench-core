# Change for 6.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 6.21.3

Released: 2021-05-18

### Changes

* Update skeleton to match v8.5.18.

### Fixes

* Fixes missing `PHPUnit\Util\Test::parseTestMethodAnnotations()` on PHPUnit 10.

## 6.21.2

Released: 2021-05-12

### Changes

* Check whether skeleton `vendor` is an actual directory before trying to symlink with base directory `vendor`.

## 6.21.1

Released: 2021-04-21

### Changes

* Update skeleton to match v8.5.16.

## 6.21.0

Released: 2021-04-06

### Added

* Added capability to fetch package discovery from root project.
* Added database-specific environment variables based on ChipperCI.

### Changes

* Allow configuration to be loaded from `Application::basePath()` instead of hardcoded value.

## 6.20.0

Released: 2021-03-31

### Added

* Added ability to run multiple database by adding database specific environment variable. E.g: `MYSQL_HOST`, `POSTGRES_HOST` and `MSSQL_HOST` instead of just `DB_HOST`.

### Changes

* Use `getcwd()` instead of relative path to setup `TESTBENCH_WORKING_PATH` constant when executing it via `bootstrap/app.php`.
* Accept `APP_BASE_PATH` environment variable to configure `getBasePath()`.

## 6.19.1

Released: 2021-03-24

### Changes

* Update Laravel skeleton.
  - Update `validation` language file.

## 6.19.0

Released: 2021-03-21

### Added

* Added `TESTBENCH_WORKING_DIRECTORY` constant.

### Removed

* Remove package discovery for `Orchestra\Testbench\Foundation\TestbenchServiceProvider`, the service provider will only be registered from CLI Commander.

## 6.18.0

Released: 2021-03-16

### Added

* Added support for PHPUnit 10.

### Changes

* Update Laravel skeleton.
  - Add `Date` aliases.
  - Update `logging` configuration.
  - Update `validation` language file.

## 6.17.1

Released: 2021-03-10

### Changes

* Update Laravel skeleton.
  - Update `queue` configuration.
  - Update `validation` language file.

## 6.17.0

Released: 2021-03-08

### Added

* Added `Orchestra\Testbench\Foundation\Console\Kernel` and `Orchestra\Testbench\Foundation\Http\Kernel`.

## 6.16.0

Released: 2021-02-21

### Changes

* Flush `Queue::createPayloadUsing()` on `Orchestra\Testbench\TestCase::tearDown()`.

## 6.15.2

Released: 2021-02-13

### Fixes

* Always attempt to delete `laravel/vendor` symlink folder.

## 6.15.1

Released: 2021-02-09

### Fixes

* Fixes tests.

## 6.15.0

Released: 2021-02-09

### Added

* Add `defineWebRoutes()` to automatically define routes under `web` middleware.

## 6.14.0 

Released: 2021-02-07

### Added

* Added `$loadEnvironmentVariables` property options to toggle loading `.env` file if available.

## 6.13.0

Released: 2021-01-30

### Added

* Added `dont-discover` configuration to `testbench.yaml`.

## 6.12.0

Released: 2021-01-29

### Added

* Added support for Laravel 8 parallel testing:
  - Added `package:test` command.
  - Added `Orchestra\Testbench\Foundation\TestbenchServiceProvider` class.

## 6.11.2

Released: 2021-01-21

### Fixes

* Handle exception on `Orchestra\Testbench\Console\Commander`.

## 6.11.1

Released: 2021-01-18

### Fixes

* Fixes tests example.

## 6.11.0

Released: 2021-01-17

### Changes

* Improves support for Package Discovery support on test environment and also `testbench` command.

## 6.10.0

Released: 2021-01-17

### Added

* Added `ignorePackageDiscoveriesFrom()` method to `Orchestra\Testbench\Concerns\CreatesApplication` trait to allow enable package discoveries during tests.
* `Orchestra\Testbench\Console\Commander` will automatically discover packages.

## 6.9.2

Released: 2020-12-30

### Changes

* Update Laravel skeleton.
    - Add `caches.stores.database.lock_connection` and `caches.stores.redis.lock_connection` configuration.

### Fixes

* Fixes `database/seeders` folder.

## 6.9.1

Released: 2020-12-15

### Fixes

* Hardcode `vlucas/phpdotenv` dependencies to avoid missing `Dotenv\Store\StoreInterface` interface.

## 6.9.0

Released: 2020-12-15

### Changes

* Bump `mockery/mockery` to `v1.3.2` and above.
* Opt to use `method_exists()` to detect support for `parseTestMethodAnnotations()` under `HandlesDatabases` and `HandlesRoutes` trait.
* Update `Orchestra\Testbench\Bootstrap\LoadConfiguration::getConfigurationFiles()` to return `Generator` instead of array.

## 6.8.0

Released: 2020-12-09

### Added

* Added following traits:
    - `Orchestra\Testbench\Concerns\HandlesAnnotations`.
    - `Orchestra\Testbench\Concerns\HandlesDatabases`.
    - `Orchestra\Testbench\Concerns\HandlesRoutes`.
* Added `defineRoutes()` and `defineCacheRoutes()` to group dedicated tests routing.

## 6.7.0

Released: 2020-12-01

### Added

* Added `defineEnvironment()` and `defineDatabaseMigrations()` method to `Orchestra\Testbench\TestCase`.
    - `defineEnvironment()` usage is identical to `getEnvironmentSetUp()` but the original function will remain functioning for now.
    - Use `defineDatabaseMigrations()` to load any database migrations for the tests. This will allows Testbench to loads it early on the test cycle before to avoid it being clashing usage with `DatabaseTransactions` trait.
* Add support to read environment variable from `.env` on skeleton when it's available when used with `testbench` bin command.

### Changes

* Update Laravel skeleton.
    - Remove `filesystems.cloud` configuration.

## 6.6.2

Released: 2020-11-25

### Changes

* Update Laravel skeleton.

## 6.6.1

Released: 2020-11-17

### Changes

* Update Laravel app skeleton.

### Fixes

* Use `TestCase::getName(false)` when resolving annotations for PHPUnit.

## 6.6.0

Released: 2020-11-07

### Added

* Added `Orchestra\Testbench\Concerns\InteractsWithPublishedFiles` trait.

### Changes

* Uses `PHPUnit\Util\Test` to parse annotations instead of relying on deprecated `TestCase::getAnnotations()`.

## 6.5.0

Released: 2020-10-28

### Changes

* Replace `fzaninotto/faker` with `fakerphp/faker`.

## 6.4.1

Released: 2020-10-27

### Changes

* Added support for PHP 8.

## 6.4.0 

Released: 2020-10-20

### Added

* Added ability to use custom Laravel path for `testbench` CLI.

## 6.3.0

Released: 2020-10-11

### Added

* Added `drop-sqlite-db` command.

### Changes

* Configuration changes:
  - Add `LOG_LEVEL` environment configuration.

## 6.2.0

Released: 2020-09-28

### Added

* Add following folders to Laravel skeleton:
  - `app/Console`
  - `app/Exceptions`
  - `app/Http/Controllers`
  - `app/Http/Middleware`
  - `app/Models`
  - `app/Providers`
  - `database/seeds`

## 6.1.1

Released: 2020-09-26

### Fixes

* Allows to skipped `env` key from `testbench.yaml`.

## 6.1.0

Released: 2020-09-24

### Added

Added experimental support for running artisan commands outside of Laravel. e.g:

    ./vendor/bin/testbench migrate

This would allows you to setup the testing environment before running `phpunit` instead of executing everything from within `TestCase::setUp()`.

### Changes

* `Orchestra\Testbench\TestCase` now uses `Illuminate\Foundation\Testing\Concerns\InteractsWithTime`.

## 6.0.1

Released: 2020-09-09

### Changes

* Throw explicit exception when using `withFactories()` without `laravel/legacy-factories`.

## 6.0.0

Released: 2020-09-08

### Added

* Added `Orchestra\Testbench\Factories\UserFactory` to handle `Illuminate\Foundation\Auth\User` model.
* Automatically autoloads `Illuminate\Database\Eloquent\LegacyFactoryServiceProvider` if the service provider exists.

### Changes

* Update support for Laravel Framework v8.
* Increase minimum PHP version to 7.3 and above (tested with 7.3 and 7.4).
* Configuration changes:
    - Changed `auth.providers.users.model` to `Illuminate\Foundation\Auth\User`.
    - Changed `queue.failed.driver` to `database-uuid`.
