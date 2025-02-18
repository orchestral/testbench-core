# Changes for 10.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 10.0.0

### Changes

* Update support for Laravel Framework v12.
* Update `Orchestra\Testbench\TestCase` to use `Illuminate\Foundation\Testing\Concerns\InteractsWithViews` trait.

### Removed

* Remove deprecated methods in `Orchestra\Testbench\Concerns\CreatesApplication` trait:
    - `getBasePath()`
    - `getDefaultApplicationBootstrapFile()`
* Remove deprecated methods in `Orchestra\Testbench\Concerns\InteractsWithMigrations` trait:
    - `loadMigrationsWithoutRollbackFrom()`
    - `loadLaravelMigrationsWithoutRollback()`
    - `runLaravelMigrationsWithoutRollback()`
