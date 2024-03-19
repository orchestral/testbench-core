# Changes for 9.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

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
