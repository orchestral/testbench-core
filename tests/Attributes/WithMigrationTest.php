<?php

namespace Orchestra\Testbench\Attributes;

use PHPUnit\Framework\TestCase;

class WithMigrationTest extends TestCase
{
    /** @test */
    public function it_can_be_resolved()
    {
        $this->assertSame(['laravel'], (new WithMigration())->types);
        $this->assertSame(['laravel', 'queue'], (new WithMigration('queue'))->types);
    }
}
