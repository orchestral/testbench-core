<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\Concerns\CreatesApplication;
use Orchestra\Testbench\Foundation\Application as Testbench;
use PHPUnit\Framework\TestCase;

class CreatesApplicationTest extends TestCase
{
    use CreatesApplication;

    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        Testbench::flushState();
    }

    /** @test */
    public function it_properly_loads_laravel_application()
    {
        $app = $this->createApplication();

        $this->assertInstanceOf(Application::class, $app);
        $this->assertTrue($app->bound('config'));
        $this->assertTrue($app->bound('view'));
    }
}
