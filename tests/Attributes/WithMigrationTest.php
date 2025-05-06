<?php

namespace Orchestra\Testbench\Attributes;

use Orchestra\Testbench\PHPUnit\TestCase;

class WithMigrationTest extends TestCase
{
    /** @test */
    public function it_can_be_resolved()
    {
        $this->assertSame(['laravel'], (new WithMigration)->types);
        $this->assertSame(['queue'], (new WithMigration('queue'))->types);
    }
}
