<?php

namespace Orchestra\Testbench\Tests\Foundation\Console;

use Orchestra\Testbench\Tests\Workbench\Console\DropSqliteDbCommandTest as TestCase;

/**
 * @requires OS Linux|DAR
 *
 * @group database
 */
class DropSqliteDbCommandTest extends TestCase
{
    protected $namespace = 'workbench';
}
