<?php

use function Orchestra\Testbench\default_skeleton_path;
use function Orchestra\Testbench\laravel_version_compare;
use function Orchestra\Testbench\package_path;
use function Orchestra\Testbench\phpunit_version_compare;
use function Orchestra\Testbench\workbench_path;
use function PHPStan\Testing\assertType;

assertType('string', default_skeleton_path());
assertType('string', default_skeleton_path('app'));
assertType('string', default_skeleton_path('app', '.gitkeep'));
assertType('string', default_skeleton_path(['app', '.gitkeep']));

assertType('string', package_path());
assertType('string', package_path('laravel'));
assertType('string', package_path('laravel', 'app', '.gitkeep'));
assertType('string', package_path(['laravel', 'app', '.gitkeep']));

assertType('string', workbench_path());
assertType('string', workbench_path('app'));
assertType('string', workbench_path('app', 'Providers'));
assertType('string', workbench_path(['app', 'Providers']));

assertType('bool', laravel_version_compare('7.0.0', '>='));
assertType('int', laravel_version_compare('7.0.0'));

assertType('bool', phpunit_version_compare('9.0.0', '>='));
assertType('int', phpunit_version_compare('9.0.0'));
