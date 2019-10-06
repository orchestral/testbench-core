# Change for 5.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## Unreleased

### Changes

* Rename default `Redis` alias under `app.aliases` to `RedisManager` to avoid incompatibility when running tests using `phpredis` extension.
