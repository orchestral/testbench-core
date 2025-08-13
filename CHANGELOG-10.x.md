# Changes for 10.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 10.6.0

Released: 2025-08-12

### Changes

* Supports for Laravel Framework 12.23.2 or above (fixed integration with PHPUnit 12.3.4).

## 10.5.0

Released: 2025-08-07

### Changes

* Supports PHPUnit 12.3.
* Remove temporary SQLite database files available via `journal_mode` configuration.
* Convert `Collection::make()` to `new Collection()`.

## 10.4.0

Released: 2025-06-08

### Changes

* Supports PHPUnit 12.2.
* `Orchestra\Foundation\Env` now extends `Orchestra\Sidekick\Env`.
* Update skeleton's configuration.

## 10.3.0

Released: 2025-05-12

### Changes

* Requires Laravel Framework 12.8.0 and above.
* `Orchestra\Testbench\PHPUnit\TestCase` now implements `Orchestra\Testbench\Concerns\InteractsWithMockery`.

### Fixes

* Fix handling deprecations logging when logger is not not available when running tests.

## 10.2.3

Released: 2025-05-07

### Changes

* Flush `Illuminate\Database\Eloquent\Model::automaticallyEagerLoadRelationships()` state between tests if the method exists.

## 10.2.2

Released: 2025-04-27

### Changes

* Flush `Illuminate\Database\Eloquent\Model` states between tests.

## 10.2.1

Released: 2025-04-13

### Changes

* Remove `symfony/polyfill-php84`.

## 10.2.0

Released: 2025-04-06

### Added

* Add ability to pass `Closure` to `Orchestra\Testbench\remote()` function.

### Changes

* Add support for PHPUnit 12.1.
* Refactor `Orchestra\Testbench\remote()` function to use `Orchestra\Testbench\Foundation\Process\RemoteCommand`.
* Rename `TESTBENCH_ENVIRONMENT_FILE_USING` to `TESTBENCH_ENVIRONMENT_FILE_USING` (internal environment variable).

## 10.1.0

Released: 2025-03-06

### Added

* Added `resolvePhpUnitTestClassName()` and `resolvePhpUnitTestMethodName()` to `Orchestra\Testbench\Concerns\InteractsWithPHPUnit` trait.

### Changes

* Allows `usesTestingFeature()` to register attribute directly on test method.
* Improves `vendor` detection on the default skeleton.
* Utilise `Orchestra\Sidekick\is_symlink()` function instead of `is_link()` to improves support on Windows.
* Use `::class` instead of `get_class()`.
* Delete `vendor` symlink via `package:purge-skeleton` command.

### Fixes

* Fix static variable via `Orchestra\Testbench\Attributes\UsesVendor::beforeEach()` method.

### Deprecate

* Deprecate following PHPUnit annotations:
  - `@environment-setup`
  - `@define-env`
  - `@define-database`
  - `@define-route`

## 10.0.3

Released: 2025-03-03

### Fixes

* Fix `Orchestra\Testbench\Attributes\UsesVendor` causes IoC Container to be out of sync.

## 10.0.2

Released: 2025-02-25

### Changes

* Revert `filesystems.disks.local.serve` default configuration value to `true`.

## 10.0.1

Released: 2025-02-24

### Changes

* Set `filesystems.disks.local.serve` default configuration value to `false`.

## 10.0.0

Released: 2025-02-24

### Changes

* Update support for Laravel Framework v12.
* Update `Orchestra\Testbench\TestCase` to use `Illuminate\Foundation\Testing\Concerns\InteractsWithViews` trait.

### Removed

* Remove deprecated functions:
    - `Orchestra\Testbench\once()`
    - `Orchestra\Testbench\transform_relative_path()`
* Remove deprecated methods in `Orchestra\Testbench\Concerns\CreatesApplication` trait:
    - `getBasePath()`
    - `getDefaultApplicationBootstrapFile()`
* Remove deprecated methods in `Orchestra\Testbench\Concerns\InteractsWithMigrations` trait:
    - `loadMigrationsWithoutRollbackFrom()`
    - `loadLaravelMigrationsWithoutRollback()`
    - `runLaravelMigrationsWithoutRollback()`
