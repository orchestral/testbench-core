# Change for 3.8

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 3.8.8

Released: 2019-12-10

### Added

* Add `storage/app/public` folder on skeleton directory.

## 3.8.7

Released: 2019-09-11

### Added

* Added `Orchestra\Testbench\Concerns\Database\WithSqlite` trait.

### Changes

* Update Laravel 5.8 skeleton.
* Recommend to be used with Laravel Framework v5.8.35+.

## 3.8.6

Released: 2019-08-04

### Changes

* Teardown test suite after using `fail()` method.
* Use `static function` rather than `function` whenever possible, the PHP engine does not need to instantiate and later GC a `$this` variable for said closure.

## 3.8.5

Released: 2019-06-10

### Added

* Added `Orchestra\Testbench\Concerns\CreatesApplication::resetApplicationArtisanCommands()`.

### Changes

* Reset registered artisan commands when running migration from `Orchestra\Testbench\Concerns\WithLoadMigrationsFrom::loadMigrationsFrom()`.

## 3.8.4

Released: 2019-05-30

### Changes

* Update Laravel 5.8 skeleton.
* Bump suggested minimum Laravel Framework version to `5.8.19`.

## 3.8.3

Released: 2019-05-13

### Changes

* Update Laravel 5.8 skeleton.
* Bump suggested minimum Laravel Framework version to `5.8.15`.

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
