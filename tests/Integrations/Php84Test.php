<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;
use function Orchestra\Testbench\laravel_migration_path;

class Php84Test extends TestCase
{
    #[Test]
    public function it_can_run_deprecated_function()
    {
        $path = laravel_migration_path();
    }
}
