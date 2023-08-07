# Change for 8.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 8.6.0

Released: 2023-08-08

### Added

* Added new Workbench support (experimental feature).
    - Register routes under `/_workbench` prefix.
    - Automatically run configured seeds when executing `migrate:fresh` and `migrate:refresh`
    - Bind `Orchestra\Testbench\Contracts\Config` to IoC Container and introduce the new `Orchestra\Testbench\workbench` helper function.
    - Add `workbench:install`, `workbench:create-sqlite-db` and `workbench:drop-sqlite-db` commands.

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
