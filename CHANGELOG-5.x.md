# Changes for 5.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 5.7.0 

Released: 2020-10-20

### Added

* Added ability to use custom Laravel path for `testbench` CLI.

## 5.6.0

Released: 2020-10-11

### Added

* Added `drop-sqlite-db` command.

## 5.5.0

Released: 2020-09-28

### Added

* Add following folders to Laravel skeleton:
  - `app/Console`
  - `app/Exceptions`
  - `app/Http/Controllers`
  - `app/Http/Middleware`
  - `app/Providers`
  - `database/seeds`

## 5.4.1

Released: 2020-09-26

### Fixes

* Fixes Dotenv usage with `testbench` command.

## 5.4.0

Released: 2020-09-25

### Added

Added experimental support for running artisan commands outside of Laravel. e.g:

    ./vendor/bin/testbench migrate

This would allows you to setup the testing environment before running `phpunit` instead of executing everything from within `TestCase::setUp()`.

## 5.3.1

Released: 2020-08-31

### Changes

* Avoid migration class name collision with Laravel default migrations by prefixing it with `testbench_`.

## 5.3.0

Released: 2020-08-31

### Added

* Added `Orchestra\Testbench\Concerns\Testing::afterApplicationRefreshed()` callback.

### Fixes

* Add missing `Closure` import to `Orchestra\Testbench\Concerns\Database\WithSqlite`.

## 5.2.0

Released: 2020-08-18

### Added

* Added `Orchestra\Testbench\Concerns\WithLaravelMigrations::runLaravelMigrations()` to run all registered Laravel migrations on `setUp` and make a clean rollback on `tearDown`.

## 5.1.4

Released: 2020-05-05

### Changes

* Update Laravel 7.x skeleton:
    - Add `mail.mailers.stmp.auth_mode` configuration.

## 5.1.3

Released: 2020-04-11

### Changes

* Update Laravel 7.x skeleton.

## 5.1.2

Released: 2020-03-31

### Changes

* Update Laravel 7.x skeleton:
    - Rename `filesystems.disk.s3.url` to `filesystems.disk.s3.endpoint`.

## 5.1.1

Released: 2020-03-16

### Changes

* Update Laravel 7.x skeleton:
    - Update `cors.exposed_headers` and `cors.max_age` default configuration value.
    - Add `mailers.smtp.timeout` configuration options.
    - Update `session` configuration file.

## 5.1.0

Released: 2020-03-11

### Changes

* Recommend to be used with Laravel Framework v7.1.0+.

## 5.0.2

Released: 2020-03-07

### Changes

* Update Laravel 7.x skeleton.
    - Cast `app.debug` value to `boolean`.
    - Add `queue.connections.sqs.suffix` configuration, use `SQS_SUFFIX` from environment variable.
    - Remove `view.expires`, feature has been reverted.
* Recommend to be used with Laravel Framework v7.0.6+.

## 5.0.1

Released: 2020-03-03

### Changes

* Update Laravel skeleton:
    - Add missing `array` mail transport.

## 5.0.0

Released: 2020-03-02

### Changes

* Change `Exception` typehint to `Throwable` on `Orchestra\Testbench\Console\Kernel`, `Orchestra\Testbench\Exceptions\Handler`.
* Change referenced class moved to `Illuminate\Testing` namespace. 
* Update Laravel 7 skeleton:
    - Rename default `Redis` alias under `app.aliases` to `RedisManager` to avoid incompatibility when running tests using `phpredis` extension.
    - Add `Http` alias under `app.aliases`.
    - Add `config/cors.php`.
    - Update `database`, `filesystem`, `mail`, `session` and `view` configuration file.
