# Change for 8.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 8.15.2

Released: 2023-11-21

### Added

* Added `encode()` method to `Orchestra\Testbench\Foundation\Env` class.

### Fixes

* Fixes registering discovery paths when the path doesn't exist.

## 8.15.1

Released: 2023-11-10

### Changes

* Move `$setupHasRun` property to `Orchestra\Testbench\Concerns\ApplicationTestingHooks`.

## 8.15.0

Released: 2023-11-10

### Added

* Added new PHPUnit Attribute to run the default `laravel`, `cache`, `notifications`, `queue` and `session` database migrations using `Orchestra\Testbench\Attributes\WithMigration`.
* Added `Orchestra\Testbench\defined_environment_variables()` function.
* Added `Orchestra\Testbench\laravel_migration_path()` function.
* Added `Orchestra\Testbench\remote()` function.

### Changes

* Mark the following classes as `@api`:
    - `Orchestra\Testbench\Foundation\Application`
    - `Orchestra\Testbench\Foundation\Config`
    - `Orchestra\Testbench\Foundation\Env`
* Cache results from `Orchestra\Testbench\PHPUnit\AttributeParser`.

## 8.14.4

Released: 2023-11-02

### Changes

* Update skeleton to match v10.2.8.

## 8.14.3

Released: 2023-10-31

### Changes

* Update skeleton to match v10.2.7.

## 8.14.2

Released: 2023-10-30

### Added

* Added `Orchestra\Testbench\Concerns\ApplicationTestingHooks` concern based from `Orchestra\Testbench\Concerns\Testing`.

## 8.14.1

Released: 2023-10-24

### Fixes

* Fixes compatibility with Testbench Dusk when handling PHPUnit Attributes.

## 8.14.0

Released: 2023-10-24

### Added

* Added `Orchestra\Testbench\Workbench\Workbench` to handle integrations with Workbench.
* Added `Orchestra\Testbench\Foundation\Config::getWorkbenchDiscoversAttributes()` method.
* Added `Orchestra\Testbench\Concerns\Database\InteractsWithSqliteDatabaseFile` trait.
* Added following methods to `Orchestra\Testbench\Foundation\Application`:
  - `make()`
  - `makeFromConfig()`
  - `createFromConfig()`
* Added support for PHPUnit Attributes as replacements to Annotations:
  - `@define-env` and `@environment-setup` will be replaced with `Orchestra\Testbench\Attributes\DefineEnvironment`.
  - `@define-db` will be replaced with `Orchestra\Testbench\Attributes\DefineDatabase`.
  - `@define-route` will be replaced with `Orchestra\Testbench\Attributes\DefineRoute`.

### Fixes

* Fixes generating path using `Orchestra\Testbench\package_path()` and `Orchestra\Testbench\workbench_path()`.

### Deprecated

* Deprecate `Orchestra\Testbench\Concerns\WithFactories`.

### Removed

* Remove `Orchestra\Testbench\Foundation\Bootstrap\StartWorkbench`, use `Orchestra\Testbench\Workbench\Workbench::start()` or `Orchestra\Testbench\Workbench\Workbench::startWithProviders()` instead.

## 8.13.0

Released: 2023-10-09

### Changes

* Code refactors.
* Mark `Orchestra\Testbench\Bootstrap\LoadEnvironmentVariables` class as `@internal`.

## 8.12.1

Released: 2023-09-25

### Changes

* Add `Orchestra\Testbench\Foundation\Config::cacheFromYaml()` to cache `testbench.yaml` for testing environment.
* Code refactors.

## 8.12.0

Released: 2023-09-25

### Added

* Added `cachedConfigurationForWorkbench()` to `Orchestra\Testbench\Concern\InteractsWithWorkbench` trait.
* Add the ability to read `TESTBENCH_WORKING_PATH` from environment variables for Testbench Dusk usage.
* Supports Workbench `discovers` configuration.
* Add the ability to properly forward Environment Variables.
* Add `usesSqliteInMemoryDatabaseConnection` to `Orchestra\Testbench\Concerns\HandlesDatabases` trait.


## 8.11.3

Released: 2023-09-25

### Fixes

* Fixes deferring Laravel Migrations when TestCase uses `Illuminate\Foundation\Testing\RefreshDatabase`.

## 8.11.2

Released: 2023-09-21

### Changes

* Allow deferring Laravel Migrations when TestCase also uses `Illuminate\Foundation\Testing\RefreshDatabase` or `Illuminate\Foundation\Testing\LazilyRefreshDatabase`.

## 8.11.1

Released: 2023-09-19

### Fixes

* Fixes `cleanUpPublishedFiles` to assign path from `base_path()` before using `glob`.

## 8.11.0

Released: 2023-09-19

### Added

* Added methods to `Orchestra\Testbench\Concerns\InteractsWithPublishedFiles` trait:
    - `assertMigrationFileExists`.
    - `assertMigrationFileNotExists`.

### Changes

* Rename methods in `Orchestra\Testbench\Concerns\InteractsWithPublishedFiles` trait:
    - `cleanUpFiles` to `cleanUpPublishedFiles`.
    - `cleanUpMigrationFiles` to `cleanUpPublishedMigrationFiles`.
    - `getMigrationFile` to `findFirstPublishedMigrationFile`.

## 8.10.2

Released: 2203-09-14

### Changes

* Allow passing wildcard filenames to `Orchestra\Testbench\Concerns\InteractsWithPublishedFiles::$files` property.
* Allow using custom directory on `assertMigrationFileContains` and `assertMigrationFileNotContains` from `Orchestra\Testbench\Concerns\InteractsWithPublishedFiles` trait.
* Allow to use `Orchestra\Testbench\Foundation\Config` on `Orchestra\Testbench\container` helper function.

## 8.10.1

Released: 2023-09-09

### Changes

* Prevents loading Laravel Migrations using `Orchestra\Testbench\Concerns\WithLaravelMigrations` when TestCase class also uses `Orchestra\Testbench\Concerns\WithWorkbench` with `workbench.install=true` configuration.

## 8.10.0

Released: 2023-08-29

### Added

* Add ability to automatically run default Laravel migrations using `Orchestra\Testbench\Concerns\WithLaravelMigrations`.
* Add Console Actions classes:
  - `Orchestra\Testbench\Foundation\Console\Actions\DeleteFiles`
  - `Orchestra\Testbench\Foundation\Console\Actions\DeleteDirectories`
  - `Orchestra\Testbench\Foundation\Console\Actions\EnsureDirectoryExists`
  - `Orchestra\Testbench\Foundation\Console\Actions\GeneratesFile`

## 8.9.1

Released: 2023-08-22

### Changes

* Allow using `$model` property override when extending `Orchestra\Testbench\Factories\UserFactory`.

## 8.9.0

Released: 2023-08-19

### Added

* Added new `workbench.welcome` configuration option.

### Changes

* Allow `testbench.yaml` configuration fallback similar to `.env`.
* Utilise `Illuminate\Support\LazyCollection`.

## 8.8.4

Released: 2023-08-18

### Changes

* Skip loading `Orchestra\Workbench\WorkbenchServiceProvider` when applying `Orchestra\Testbench\Concerns\WithWorkbench`.

## 8.8.3

Released: 2023-08-17

### Fixes

* Fixes configuration leak when running some TestCase without `Orchestra\Testbench\Concerns\WithWorkbench`.

## 8.8.2

Released: 2023-08-17

### Changes

* Disable Composer default timeout when using `serve` command under Composer's script.

### Removed

* Remove `Orchestra\Testbench\Workbench` classes and functionality is now provided from `orchestra/workbench`.

## 8.8.1

Released: 2023-08-16

### Added

* Readd deprecated `Orchestra\Testbench\Foundation\Console\DevToolCommand` for integration compatibility.

## 8.8.0

Released: 2023-08-15

### Added

* Added `package:purge-skeleton` command.
* Added `Orchestra\Testbench\Concerns\Database\InteractsWithSqliteDatabaseFile` trait.
* Added `Orchestra\Testbench\package_path()` function.
* Added support for `orchestra/workbench`.

### Changes

* Rename `Orchestra\Testbench\Workbench\Bootstrap\StartWorkbench` to `Orchestra\Testbench\Foundation\Bootstrap\StartWorkbench`.

### Fixes

* Fixes `serve` command usage.

## 8.7.1

Released: 2023-08-12

### Fixes

* Fixes class namespace.

## 8.7.0

Released: 2023-08-12

### Added

* Added following events:
    - `Orchestra\Testbench\Workbench\Events\WorkbenchInstallStarted`
    - `Orchestra\Testbench\Workbench\Events\WorkbenchInstallEnded`

### Changes

* Change `HandlesRoutes` loading sequence to match common Laravel bootstrap steps.
* Refactor `HandlesAnnotations` and `InteractsWithPHPUnit` traits.
* Workbench integration improvements.

## 8.6.3

Released: 2023-08-11

### Added

* Added following events:
    - `Orchestra\Testbench\Foundation\Events\ServeCommandStarted`
    - `Orchestra\Testbench\Foundation\Events\ServeCommandEnded`

### Changes

* Update `workbench` configuration schema.

### Fixes

* Fixes `Illuminate\Foundation\Application::runningUnitTests()` detection.

## 8.6.2

Released: 2023-08-10

### Fixes

* Fixes `app()->environment()` detection when creating application `Orchestra\Testbench\Concerns\CreatesApplication` outside of `PHPUnit`.
* Fixes error `Undefined array key "autoload-dev"` when executing `workbench:install` command.

## 8.6.1

Released: 2023-08-09

### Added

* Add new `Orchestra\Testbench\Concerns\InteractsWithPHPUnit` to handle `CreatesApplication` within PHPUnit.

### Fixes

* Fixes `workbench.start` path when accessing the `/` route return 404.
* Only Configure `TESTBENCH_APP_BASE_PATH` environment variable only when running under tests.

## 8.6.0

Released: 2023-08-08

### Added

* Added new Workbench support (experimental feature).
    - Register routes under `/_workbench` prefix.
    - Automatically run configured seeds when executing `migrate:fresh` and `migrate:refresh`
    - Bind `Orchestra\Testbench\Contracts\Config` to IoC Container and introduce the new `Orchestra\Testbench\workbench` and `Orchestra\Testbench\workbench_path` helper function.
    - Add `workbench:install`, `workbench:create-sqlite-db` and `workbench:drop-sqlite-db` commands.
* Add new `Orchestra\Testbench\Concerns\WithWorkbench` to automatically loads configuration from `testbench.yaml` when running tests.

### Deprecated

* Deprecated `package:devtool`, `package:create-sqlite-db` and `package:drop-sqlite-db` commands.

## 8.5.9

Released: 2023-07-12

### Changes

* Update skeleton to match v10.2.5.

## 8.5.8

Released: 2023-06-22

### Changes

* Update skeleton to match v10.2.4.

## 8.5.7

Released: 2023-06-13

### Changes

* Bump minimum `laravel/framework` to `10.13.5`.
* Automate registering `tearDownInteractsWithPublishedFiles()` from `setUpInteractsWithPublishedFiles()` method.

## 8.5.6

Released: 2023-06-08

### Changes

* Remove `.env.testbench` out of skeleton directory.

## 8.5.5

Released: 2023-06-07

### Fixes

* Avoid replacing `$app->environmentFile()` with `.env.testbench` to avoid any regression to Laravel Framework tests.

## 8.5.4

Released: 2023-06-07

### Fixes

* Fixes issue where PHPUnit would throws "warning" when `.env` file doesn't exists with certain configuration.

## 8.5.3

Released: 2023-05-26

### Changes

* Update skeleton to match v10.2.2.

## 8.5.2

Released: 2023-05-17

### Changes

* Update skeleton to match v10.2.1.

## 8.5.1

Released: 2023-05-09

### Changes

* Bump minimum `laravel/framework` to `10.10.0`.
* Update skeleton to match v10.2.0.

## 8.5.0

Released: 2023-04-18

### Added

* Added `Orchestra\Testbench\after_resolving` helper function.

### Changes

* Update skeleton to match v10.1.0.
* Bump minimum `laravel/framework` to `10.8.0`.

## 8.4.2

Released: 2023-04-14

### Changes

* Supports PHPUnit 10.1.

## 8.4.1

Released: 2023-04-12

### Changes

* Update skeleton to match v10.0.6.
* Avoid declaring `Orchestra\Testbench\Concerns\Testing::setUpTheTestEnvironmentTraitToBeIgnored()` as `abstract` method.

## 8.4.0

Released: 2023-04-05

### Changes

* Add `setUpTheTestEnvironmentTraitToBeIgnored()` method to determine `setup<Concern>` and `teardown<Concern>` with imported traits that should be used on a given trait.
* Bump minimum `laravel/framework` to `10.6.1`.

## 8.3.1

Released: 2023-04-02

### Fixes

* Fixes `Orchestra\Testbench\Foundation\Config::addProviders()` usage.
* Fixes `Orchestra\Testbench\transform_relative_path()` logic.

## 8.3.0

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

## 8.2.0

Released: 2023-03-27

### Added

* Add supports for `setup<Concern>` and `teardown<Concern>` with imported traits.

### Changes

* Move PHPUnit 9 support to legacy:
    - Recommend using PHPUnit 10 whenever possible. 
    - Remove deprecation handling support for PHPUnit 9.
    - Only recommend using `package:test` and `--parallel` with PHPUnit 10.

-------------

> **Warning**: Breaking change is possible if your package contains any traits with `setup<TraitClassName>` or `teardown<TraitClassName>`
>
> This version now will automatically run those methods during application bootstrap and terminate to be consistent with Laravel Framework implementations.

## 8.1.2

Released: 2023-03-22

### Fixes

* Fixes resetting `testbench.yaml` from backup is only needed if the backup file exists.

## 8.1.1

Released: 2023-03-20

### Changes

* Update `package:test` available options.

## 8.1.0

Released: 2023-03-18

### Changes

* Bump minimum `laravel/framework` to `10.4.0`.
* Update available middlewares with content from `laravel/laravel`.

## 8.0.5

Released: 2023-03-10

### Changes

* Bump minimum `laravel/framework` to `10.3.3`.

## 8.0.4

Released: 2023-03-09

### Changes

* Bump minimum `laravel/framework` to `10.3.1`.

## 8.0.3

Released: 2023-02-24

### Changes

* Bump minimum `laravel/framework` to `10.1.4`.

## 8.0.2

Released: 2023-02-21

### Fixes

* Fixes `app.asset_url` config default value from `'/'` to `null`.

## 8.0.1

Released: 2023-02-17

### Changes

* Bump minimum `laravel/framework` to `10.0.3`.
* Use available `$_composer_autoload_path` from `composer-runtime-api`.

## 8.0.0

Released: 2023-02-14

### Added

* Added support for PHPUnit 10.

### Changes

* Update support for Laravel Framework v10.
* Increase minimum PHP version to 8.1 and above (tested with 8.1 and 8.2).
