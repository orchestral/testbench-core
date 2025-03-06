# Changes for 10.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## Unreleased

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

Changelog: [v10.0.3...10.x](https://github.com/orchestral/testbench-core/compare/v10.0.3...10.x)

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
