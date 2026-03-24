# Changes for 11.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 11.1.0

Released: 2026-03-24

### Changes

* Supports for Laravel Framework 13.1.1.
* Add `TESTBENCH_USER_MODEL` environment variable when running `serve` command.
* Utilise `Orchestra\Testbench\Foundation\Console\TerminatingConsole` when running `serve`.

## 11.0.1

Released: 2026-03-18

### Fixes

* Fix `--parallel` compatibility with `WithFixtures` trait.

## 11.0.0

Released: 2026-03-16

### Changes

* Update support for Laravel Framework v13.

### Removed

* Remove deprecated `Orchestra\Testbench\Foundation\Env` class.
* Remove deprecated `Orchestra\Testbench\Foundation\Console\Concerns\HandleTerminatingConsole` trait.
* Remove deprecated `Orchestra\Testbench\Foundation\Console\Actions\Action::pathLocation()` method.
* Remove deprecated `Orchestra\Testbench\laravel_migration_path()` function.
* Remove supports for deprecated annotations: 
  - `@define-env`
  - `@environment-setup`
  - `@define-db`
  - `@define-route`
