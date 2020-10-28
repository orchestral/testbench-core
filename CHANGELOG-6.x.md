# Change for 6.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 6.5.0

Released: 2020-10-28

### Changes

* Replace `fzaninotto/faker` with `fakerphp/faker`.

## 6.4.1

Released: 2020-10-27

### Changes

* Added support for PHP 8.

## 6.4.0 

Released: 2020-10-20

### Added

* Added ability to use custom Laravel path for `testbench` CLI.

## 6.3.0

Released: 2020-10-11

### Added

* Added `drop-sqlite-db` command.

### Changes

* Configuration changes:
  - Add `LOG_LEVEL` environment configuration.

## 6.2.0

Released: 2020-09-28

### Added

* Add following folders to Laravel skeleton:
  - `app/Console`
  - `app/Exceptions`
  - `app/Http/Controllers`
  - `app/Http/Middleware`
  - `app/Models`
  - `app/Providers`
  - `database/seeds`

## 6.1.1

Released: 2020-09-26

### Fixes

* Allows to skipped `env` key from `testbench.yaml`.

## 6.1.0

Released: 2020-09-24

### Added

Added experimental support for running artisan commands outside of Laravel. e.g:

    ./vendor/bin/testbench migrate

This would allows you to setup the testing environment before running `phpunit` instead of executing everything from within `TestCase::setUp()`.

### Changes

* `Orchestra\Testbench\TestCase` now uses `Illuminate\Foundation\Testing\Concerns\InteractsWithTime`.

## 6.0.1

Released: 2020-09-09

### Changes

* Throw explicit exception when using `withFactories()` without `laravel/legacy-factories`.

## 6.0.0

Released: 2020-09-08

### Added

* Added `Orchestra\Testbench\Factories\UserFactory` to handle `Illuminate\Foundation\Auth\User` model.
* Automatically autoloads `Illuminate\Database\Eloquent\LegacyFactoryServiceProvider` if the service provider exists.

### Changes

* Update support for Laravel Framework v8.
* Increase minimum PHP version to 7.3 and above (tested with 7.3 and 7.4).
* Configuration changes:
    - Changed `auth.providers.users.model` to `Illuminate\Foundation\Auth\User`.
    - Changed `queue.failed.driver` to `database-uuid`.
