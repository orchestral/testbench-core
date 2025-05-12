<?php

namespace Orchestra\Testbench\Attributes;

use Orchestra\Testbench\PHPUnit\TestCase;
use PHPUnit\Framework\Attributes\Test;

class WithMigrationTest extends TestCase
{
    #[Test]
    public function it_can_be_resolved()
    {
        $this->assertSame(['laravel'], (new WithMigration)->types);
        $this->assertSame(['laravel'], (new WithMigration('queue'))->types);
    }
}
