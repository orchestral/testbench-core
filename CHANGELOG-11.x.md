# Changes for 11.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 11.3.4

Released: 2026-05-20

### Changes

* Supports for Laravel Framework 13.10.0.
* Update skeleton.

## 11.3.3

Released: 2026-05-14

### Changes

* Supports for Laravel Framework 13.9.0.
* Update skeleton.

## 11.3.2

Released: 2026-05-06

### Changes

* Supports for Laravel Framework 13.7.0.
* Requires `symfony/polyfill-php84` for `Pdo\Mysql` class on PHP 8.3 and below.
* Update skeleton.

## 11.3.1

Released: 2026-04-24

### Fixes

* Fix `Orchestra\Testbench\Attributes\WithConfig` shouldn't defer setting up framework configuration.

## 11.3.0

Released: 2026-04-23

### Changes

* Loads `Orchestra\Testbench\Attributes\WithConfig` after application has been booted to allow merging configuration via Service Provider by default. Use `defer: false` parameter to disable this.
* Update skeleton.

### Fixes

* Fix missing `--without-cache` option when using `package:test` command with `nunomaduro/collision` version `8.9.4+`.

## 11.2.1

Released: 2026-04-09

### Changes

* Add `flushState()` to `FormRequest` to reset global strict mode between tests.

## 11.2.0

Released: 2026-04-07

### Changes

* Supports PHPUnit 13.1.
* Removed no longer relevants `method_exists()` and `class_exists()` usage.

## 11.1.1

Released: 2026-03-31

### Changes

* Overrides `ServeCommand::trap()` method to use `TerminatingConsole`.

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
