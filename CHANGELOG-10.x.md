# Changes for 10.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## Unreleased

### Removed

* Remove deprecated `getDefaultApplicationBootstrapFile()` method in `Orchestra\Testbench\Concerns\CreatesApplication` trait.
* Remove deprecated methods in `Orchestra\Testbench\Concerns\InteractsWithMigrations` trait:
    - `loadMigrationsWithoutRollbackFrom()`
    - `loadLaravelMigrationsWithoutRollback()`
    - `runLaravelMigrationsWithoutRollback()`
