<?php

namespace Orchestra\Testbench\Tests\Databases;

use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DatabaseTest extends TestCase
{
    #[Test]
    public function testbench_doesnt_automatically_create_database_connection()
    {
        $this->assertCount(0, DB::getConnections());
    }
}
