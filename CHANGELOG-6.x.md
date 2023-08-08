# Change for 6.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 6.33.0

Released: 2023-08-08

### Added

* Added new Workbench support (experimental feature).
    - Register routes under `/_workbench` prefix.
    - Automatically run configured seeds when executing `migrate:fresh` and `migrate:refresh`
    - Bind `Orchestra\Testbench\Contracts\Config` to IoC Container and introduce the new `Orchestra\Testbench\workbench` helper function.
* Add PHPStan analysis.
* Add new `Orchestra\Testbench\Concerns\WithWorkbench` to automatically loads configuration from `testbench.yaml` when running tests.

## 6.32.0

Released: 2023-06-13

### Added

* `Orchestra\Testbench\Bootstrap\LoadEnvironmentVariables` to manage loading `.env` file during tests, backport from Testbench Core 8 releases.

### Changes

* Automate registering `tearDownInteractsWithPublishedFiles()` from `setUpInteractsWithPublishedFiles()` method.

## 6.31.2

Released: 2023-04-11

### Changes

* Add `setUpTheTestEnvironmentTraitToBeIgnored()` method to determine `setup<Concern>` and `teardown<Concern>` with imported traits that should be used on a given trait.

## 6.31.1

Released: 2023-04-02

### Fixes

* Fixes `Orchestra\Testbench\Foundation\Config::addProviders()` usage.
* Fixes `Orchestra\Testbench\transform_relative_path()` logic.

## 6.31.0

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

## 6.30.0

Released: 2023-03-27

### Added

* Added `Orchestra\Testbench\Foundation\Config` to read Yaml file from `testbench.yaml`.
* Added `Orchestra\Testbench\Foundation\Application::createVendorSymlink()` method.
    - The feature uses `Orchestra\Testbench\Foundation\Bootstrap\CreateVendorSymlink`.
* Added `resolveApplicationEnvironmentVariables()` method.
* Add supports for `setup<Concern>` and `teardown<Concern>` with imported traits.

### Changes

* Bump minimum laravel/framework to `8.83.26`.
* Improves PHPUnit memory leaks.
* Refactor the following classes to match Testbench 7:
    - `Orchestra\Testbench\Concerns\HandlesRoutes`
    - `Orchestra\Testbench\Console\Commander`
    - `Orchestra\Testbench\Foundation\Application`

-------------

> **Warning**: Breaking change is possible if your package contains any traits with `setup<TraitClassName>` or `teardown<TraitClassName>`
>
> This version now will automatically run those methods during application bootstrap and terminate to be consistent with Laravel Framework implementations.
 
## 6.29.1

Released: 2022-10-11

### Fixes

* Remove `bootstrap/cache/packages.php` on vendor symlink event.

## 6.29.0

Released: 2022-08-24

### Added

* Added `loadLaravelMigrationsWithoutRollback()` and `runLaravelMigrationsWithoutRollback()` helpers.

## 6.28.1

Released: 2022-02-08

### Changes

* Update skeleton to match v8.6.11.

## 6.28.0

Released: 2022-01-13

### Changes

* Allow package discoveries by adding `$enablesPacakgeDiscoveries = true` property.
* Allow to run `defineCacheRoute()` before application is ready.
* Support defining custom `$basePath` when using `Orchestra\Testbench\container` function.

## 6.27.4

Released: 2021-12-23

### Changes

* Update skeleton to match v8.6.10.

## 6.27.3

Released: 2021-12-04

### Changes

* Improves docblock.

## 6.27.2

Released: 2021-12-02

### Changes

* Update skeleton to match v8.6.8.

## 6.27.1

Released: 2021-11-17

### Changes

* Update skeleton to match v8.6.7.

## 6.27.0

Released: 2021-11-10

### Added

* Add ability to define database migrations using `TestCase::defineDatabaseMigrationsAfterDatabaseRefreshed()` method, the method will only be executed via `Illuminate\Database\Events\DatabaseRefreshed` event.
* Add ability to destroy database migrations using `TestCase::destroyDatabaseMigrations()`.

## 6.26.0

Released: 2021-10-21

### Added

* Added draft support for PHP 8.1.
* Added `Orchestra\Testbench\container()` function to easily create an application instance.

### Changes

* Update skeleton to match v8.6.4.
* Improves docblock.

## 6.25.2

Released: 2021-09-18

### Changes

* Ability to use `App\Http\Kernel` and `App\Console\Kernel` via Commander if the class exists.

## 6.25.1

Released: 2021-09-11

### Fixes

* Fixes missing `Orchestra\Testbench\Contracts\TestCase` to `artisan()` helper function.

## 6.25.0

Released: 2021-09-08

### Added

* Add ability to define database seeder using `TestCase::defineDatabaseSeeders()` method.

### Changes

* Update skeleton to match v8.6.2.

## 6.24.1

Released: 2021-08-25

### Changes

* Update skeleton to match v8.6.1.

## 6.24.0

Released: 2021-08-12

### Changes

* Bump minimum `laravel/framework` to `8.54`.
* Update skeleton to match v8.5.24.

## 6.23.1

Released: 2021-07-14

### Changes

* Update skeleton to match v8.5.22.

## 6.23.0

Released: 2021-06-16

### Changes

* Improves generating cached routes during testing.
* Allows to loads `.env` when using `Orchestra\Testbench\Foundation\Application`.
* Update skeleton.

## 6.22.0

Released: 2021-05-25

### Added

* Added `Orchestra\Testbench\Foundation\Application` to allow creating remote application using Testbench.
* Added static public method `Orchestra\Testbench\Concerns\CreatesApplication::applicationBasePath()` to replace `getBasePath()`.

### Changes

* Update skeleton.

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
