# Changes for 9.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/testbench-core`.

## 9.0.0

Released: 2023-02-14

### Added

* Added support for PHPUnit 11.
* Added new `Orchestra\Testbench\Concerns\WithLaravelBootstrapFile` trait.

### Changes

* Update support for Laravel Framework v11.
* Increase minimum PHP version to 8.2 and above (tested with 8.2 and 8.3).

### Deprecated

* Deprecate `Orchestra\Testbench\Concerns\HandlesAnnotations` inline with PHPUnit removal support for meta-comment support using annotation.

### Removed

* Remove deprecated `Orchestra\Testbench\Concerns\Database\HandlesConnections` trait.
* Removed deprecated `create-sqlite-db` and `drop-sqlite-db` standalone commands.
