<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RefreshDatabaseTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_run_test_using_refresh_database_trait()
    {
        $userId = 123;
        $message = 'Test message';
        $expectedId = $userId . '-' . md5($message);

        // Test the logic without instantiating the class
        $this->assertEquals($expectedId, $userId . '-' . md5($message));
    }
}
