# Change for 3.9

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 3.9.7

Released: 2020-03-07

### Changes

* Update Laravel 5.8 skeleton.
* Recommend to be used with Laravel Framework v6.18.0+.

## 3.9.6

Released: 2020-01-21

### Changes

* Update Laravel 6 skeleton:
    - Update redis configurations under `database`.
    - Update `logging` to use `single` channel under `stack`.
    - Add `emergency` channel to `logging`.
    - Ensure validation text ends with `.`.

### Changes

* Update Laravel 6 skeleton:
    - Add `auth.passwords.users.throttle` configuration.

## 3.9.5

Released: 2019-12-10

### Added

* Add `storage/app/public` folder on skeleton directory.

## 3.9.4

Released: 2019-10-24

### Changes

* Update Laravel 6 skeleton:
    - Add `auth.passwords.users.throttle` configuration.

## 3.9.3

Released: 2019-10-11

### Changes

* Update support for Laravel Framework v6.2.
* Update `Orchestra\Testbench\Http\Kernel::$routeMiddleware` to include `password.confirm` middleware.
* Update Laravel 6 skeleton:
    - Add `auth.password_timeout` configuration.
    - Add `password` value for `validation` language file.

## 3.9.2

Released: 2019-09-15

### Changes

* Update Laravel 6 skeleton:
    - Add `logging.channels.null` configuration.
    - Revert Argon2 memory configuration made in v3.9.1.

## 3.9.1

Released: 2019-09-11

### Changes

* Update Laravel 6 skeleton.
* Test againsts PHP `7.4snapshot` build.

## 3.9.0

Released: 2019-09-03

### Changes

* Update support for Laravel Framework v6.0.
* Increase minimum PHP version to 7.2+ (tested with 7.2 and 7.3).
* Increase minimum PHPUnit to v8.0+.
