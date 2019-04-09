# Change for 3.8

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 3.8.2

Released: 2019-04-09

### Changes

* Update Laravel 5.8 skeleton.

## 3.8.1

Released: 2019-02-28

### Changes

* Update Laravel 5.8 skeleton.
    - Uncomment `session.expire_on_close`.
    - Use `AWS_DEFAULT_REGION` environment variable instead of `AWS_REGION` for consistency.

### Fixes

* Fixes `Orchestra\Testbench\Http\Middleware\RedirectIfAuthenticated` middleware class.

## 3.8.0

Released: 2019-02-26

### Changes

* Update support for Laravel Framework v5.8.
* Add `void` return type to `setUp()` and `tearDown()` for PHPUnit 8+ compatibility. 
