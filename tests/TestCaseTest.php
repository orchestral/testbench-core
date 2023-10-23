<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\Contracts\TestCase as TestCaseContract;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function Orchestra\Testbench\container;
use function Orchestra\Testbench\phpunit_version_compare;

class TestCaseTest extends TestCase
{
    #[Test]
    public function it_can_create_the_testcase()
    {
        $methodName = phpunit_version_compare('10', '>=')
            ? $this->name()
            : $this->getName();

        $testbench = new class($methodName) extends \Orchestra\Testbench\TestCase
        {
            //
        };

        $app = $testbench->createApplication();

        $this->assertInstanceOf(Application::class, $app);
        $this->assertEquals('UTC', date_default_timezone_get());
        $this->assertEquals('testing', $app['env']);
        $this->assertSame('testing', $app->environment());
        $this->assertTrue($app->runningUnitTests());
        $this->assertInstanceOf(ConfigRepository::class, $app['config']);

        $this->assertInstanceOf(TestCaseContract::class, $testbench);
        $this->assertTrue($testbench->isRunningTestCase());
    }

    #[Test]
    public function it_can_create_a_container()
    {
        $container = container();

        $app = $container->createApplication();

        $this->assertInstanceOf(Application::class, $app);
        $this->assertEquals('UTC', date_default_timezone_get());
        $this->assertEquals('testing', $app['env']);
        $this->assertSame('testing', $app->environment());
        $this->assertTrue($app->runningUnitTests());
        $this->assertInstanceOf(ConfigRepository::class, $app['config']);

        $this->assertFalse($container->isRunningTestCase());
    }
}
