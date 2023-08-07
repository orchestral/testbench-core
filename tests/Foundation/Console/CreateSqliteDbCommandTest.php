<?php

namespace Orchestra\Testbench\Tests\Foundation\Console;

use Orchestra\Testbench\Tests\Workbench\Console\CreateSqliteDbCommandTest as TestCase;

/**
 * @requires OS Linux|DAR
 *
 * @group database
 */
class CreateSqliteDbCommandTest extends TestCase
{
    protected $namespace = 'package';
}
