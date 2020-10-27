# Changes for 4.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 4.7.2

Released: 2020-10-25

### Changes

* Added support for PHP 8.

## 4.7.1

Released: 2020-04-11

### Changes

* Update Laravel 6.x skeleton.

## 4.7.0

Released: 2020-03-07

### Changes

* Update Laravel 6.x skeleton.
* Recommend to be used with Laravel Framework v6.18.0+.

## 4.6.0

Released: 2020-01-30

### Changes

* Bump minimum `fzaninotto/faker` version to `1.9.1`+ to properly support PHP 7.4.

## 4.5.1

Released: 2020-01-21

### Changes

* Update Laravel 6 skeleton:
    - Ensure validation text ends with `.`.

## 4.5.0

Released: 2019-12-31

### Added

* Added support for PHPUnit v9.

### Changes

* Update support for Laravel Framework v6.9.

## 4.4.2

Released: 2019-12-10

### Added

* Add `storage/app/public` folder on skeleton directory.

## 4.4.1

Released: 2019-11-23

### Fixes

* Check if `Orchestra\Testbench\Concerns\CreatesApplication` is being used by `PHPUnit\Framework\TestCase` before trying to uses `@environment-setup`
annotation support.

## 4.4.0

Released: 2019-11-22

### Added

* Added annotation based environment setup using `@environment-setup`, the annotation accept the method name string to be used for environment setup, you can call `@environment-setup` multiple times to load multiple setup per test.

## 4.3.0

Released: 2019-10-24

### Changes

* Update support for Laravel Framework v6.4.
* Update to use `realpath()` when resolving application base path. ([#31](https://github.com/orchestral/testbench-core/pull/31))
* Update Laravel 6 skeleton:
    - Add `auth.passwords.users.throttle` configuration.

## 4.2.0

Released: 2019-10-11

### Changes

* Update support for Laravel Framework v6.2.
* Update `Orchestra\Testbench\Http\Kernel::$routeMiddleware` to include `password.confirm` middleware.
* Update Laravel 6 skeleton:
    - Add `auth.password_timeout` configuration.
    - Add `password` value for `validation` language file.

## 4.1.0 

Released: 2019-10-06

### Changes

* Update support for Laravel Framework v6.1+.
* Rename default `Redis` alias under `app.aliases` to `RedisManager` to avoid incompatibility when running tests using `phpredis` extension.

## 4.0.2

Released: 2019-09-15

### Changes

* Update Laravel 6 skeleton:
    - Add `logging.channels.null` configuration.
    - Revert Argon2 memory configuration made in v4.0.1.

## 4.0.1

Released: 2019-09-11

### Changes

* Update Laravel 6 skeleton.
* Test againsts PHP `7.4snapshot` build.

## 4.0.0

Released: 2019-09-03

### Changes

* Update support for Laravel Framework v6.0.
* Increase minimum PHP version to 7.2+ (tested with 7.2 and 7.3).
* Increase minimum PHPUnit to v8.0+.
* Configuration changes:
    - `BCRYPT_ROUNDS` now defaults to `10`.
    - `REDIS_CLIENT` now defaults to `phpredis`.
    - `REDIS_CLUSTER` now defaults to `redis`.

### Breaking Changes

* Any tests requiring Redis would now requires `ext-redis` to be installed. As of now you either can setup Redis or set `REDIS_CLIENT` and `REDIS_CLUSTER` to the deprecated `predis` option.
