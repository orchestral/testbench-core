<?php

namespace Orchestra\Testbench\Attributes;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class WithMigrationTest extends TestCase
{
    #[Test]
    public function it_can_be_resolved()
    {
        $this->assertSame(['laravel'], (new WithMigration())->types);
        $this->assertSame(['queue'], (new WithMigration('queue'))->types);
    }
}
