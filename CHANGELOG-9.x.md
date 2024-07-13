# Changes for 9.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 9.2.1

Released: 2024-07-13

### Changes

* Change `resolveApplicationResolvingCallback()` method visibility from `private` to `protected`.

## 9.2.0

Released: 2024-07-13

### Added

* Added new attributes:
    - `Orchestra\Testbench\Attributes\ResolvesLaravel`
    - `Orchestra\Testbench\Attributes\UsesFrameworkConfiguration`
* Allows to discover `factories` using Workbench to map `Workbench\App\Models` to `Workbench\Database\Factories` classes.
* Allows to auto discover console command classes from `workbench/app/Console/Commands`.

### Changes

* Bump minimum support to Laravel Framework v11.11.
* Implements `JsonSerializable` to `Orchestra\Testbench\Foundation\UndefinedValue`.
* Update skeleton to use `workbench` as default environment value.
* Allow `Orchestra\Testbench\Attributes\Define` and `Orchestra\Testbench\Attributes\DefineEnvironment` to be used on the class level by [@danjohnson95](https://github.com/danjohnson95)

### Fixes

* Ensure `usesTestingFeature()` attribute registration is loaded before class attributes instead of method attributes.

## 9.1.9

Released: 2024-07-10

### Changes

* Update skeleton to match v11.1.3.
* Includes `server.php` improvements from Laravel Framework 11.14.

## 9.1.8

Released: 2024-06-28

### Changes

* Add support for PHPUnit 11.2.

## 9.1.7

Released: 2024-06-26

### Fixes

* Fixes `overrideApplicationAliases()` and `overrideApplicationProviders()` unable to override packages aliases or providers.

## 9.1.6

Released: 2024-06-10

### Changes

* Defer setting default Rate Limiter until `cache.store` is resolved.

## 9.1.5

Released: 2024-06-10

### Changes

* Fallback `app.providers` using `Illuminate\Support\DefaultProviders` when the configuration return `null`.

## 9.1.4

Released: 2024-06-06

### Changes

* Allow `$latestResponse` static property to be optional.

## 9.1.3

Released: 2024-06-04

### Fixes

* Fixes `Orchestra\Testbench\Workench\Workbench::applicationExceptionHandler()` usage to detect `Workbench\App\Exceptions\Handler` class.
* Fixes `Orchestra\Testbench\Console\Kernel` and `Orchestra\Testbench\Foundation\Console\Kernel` unable to discover commands.

## 9.1.2

Released: 2024-06-01

### Fixes

* Fixes `Orchestra\Testbench\Attributes\RequiresLaravel` attribute usage.

## 9.1.1

Released: 2024-05-23

### Changes

* Utilise `Orchestra\Testbench\package_path()` function instead of `TESTBENCH_WORKING_PATH` constant.
* Update configuration to match Laravel Framework v11.8.0.

## 9.1.0

Released: 2024-05-21

### Changes

* Uses `TESTBENCH_WORKING_PATH` from environment variable before fallback to `getcwd()`.
* PHPStan Improvements.

## 9.0.16

Released: 2024-05-09

### Changes

* Update skeleton to match v11.0.7.

## 9.0.15

Released: 2024-04-24

### Changes

* Add support for PHPUnit 11.1.

## 9.0.14

Released: 2024-04-21

### Fixes

* Fixes routing registration using macro with Workbench.

## 9.0.13

Released: 2024-04-16

### Fixes

* Fixes `serve` command.

## 9.0.12

Released: 2024-04-13

### Changes

* Allows `Orchestra\Testbench\remote` to accept `$env` with either `array` or `string`.
* Includes `TESTBENCH_PACKAGE_REMOTE=true` when running command using `Orchestra\Testbench\remote`.

### Fixes

* Fixes `runningInUnitTests()` returning `true` when not running tests via Testbench CLI.

## 9.0.11

Released: 2024-04-08

### Changes

* Flush Static Improvements.
* Revert setting `workbench` environment variable when Testbench CLI is used outside of testing. 

## 9.0.10

Released: 2024-04-05

### Fixes

* Fixes `runningInUnitTests()` returning `true` when not running tests via Testbench CLI.

## 9.0.9

Released: 2024-03-27

### Fixes

* Force reset `RefreshDatabaseState` when using `LazilyRefreshDatabase` with SQLite `:in-memory:` database connections.

## 9.0.8

Released: 2024-03-26

### Changes

* Add support for `HASH_VERIFY` environment variables.

## 9.0.7

Released: 2024-03-25

### Fixes

* Fixes `RefreshDatabase` to be executed on `tearDown()` only limited when ad-hoc migrations was added during test.

## 9.0.6

Released: 2024-03-19

### Changes

* Run `ResetRefreshDatabaseState` via `tearDownTheTestEnvironmentUsingTestCase()` method.

### Fixes

* Fixes `beforeApplicationDestroyed()` usage on `loadLaravelMigrations()` method.

## 9.0.5

Released: 2024-03-19

### Fixes

* Fixes `RefreshDatabase` usage does not reset the database migrations between tests.

## 9.0.4

Released: 2024-03-18

### Changes

* Check against `RefreshDatabaseState::$migrated` and `RefreshDatabaseState::$lazilyRefreshed` before loading migration paths to the instance of `migrator`.
* Update skeleton to match v11.0.3.

### Fixes

* Fixes `class_implements()` should only be executed if the Attribute class exists.

## 9.0.3

Released: 2024-03-14

### Changes

* Revert default skeleton database collations to `utf8mb4_unicode_ci`.

## 9.0.2

Released: 2024-03-14

### Fixes

* Testbench CLI should prioritize application kernels defined via `bootstrap/app.php` when configured using a custom skeleton.

## 9.0.1

Released: 2024-03-13

### Added

* Added `usesRefreshDatabaseTestingConcern()` helper method to `Orchestra\Testbench\Concerns\InteractsWithTestCase` trait.

### Changes

* Bump minimum `laravel/framework` to `11.0.3`.

## 9.0.0

Released: 2024-03-12

### Added

* Added support for PHPUnit 11.
* Added new `Orchestra\Testbench\Concerns\WithLaravelBootstrapFile` trait.
* Added `Orchestra\Testbench\Attributes\RequiresLaravel` attribute.
* Added `Orchestra\Testbench\load_migration_paths()` function.

### Changes

* Update support for Laravel Framework v11.
* Increase minimum PHP version to 8.2 and above (tested with 8.2 and 8.3).
* Validate `MYSQL_*`, `MSSQL_*`, `SQLITE_*` and `POSTGRES_*` environment variables before trying to override the configuration values.

### Deprecated

* Deprecate `Orchestra\Testbench\Concerns\HandlesAnnotations` in line with PHPUnit removal support for meta-comment support using annotation.

### Removed

* Remove deprecated `Orchestra\Testbench\Concerns\Database\HandlesConnections` trait.
* Removed deprecated `create-sqlite-db` and `drop-sqlite-db` standalone commands.
