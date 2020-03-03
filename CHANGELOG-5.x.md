# Change for 5.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

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
