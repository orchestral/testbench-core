<?php

namespace Orchestra\Testbench\Tests;

use function Orchestra\Testbench\phpunit_version_compare;
use PHPUnit\Framework\TestCase;

class TestCaseTest extends TestCase
{
    /** @test */
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

        $this->assertInstanceOf('\Orchestra\Testbench\Contracts\TestCase', $testbench);
        $this->assertInstanceOf('\Illuminate\Foundation\Application', $app);
        $this->assertEquals('UTC', date_default_timezone_get());
        $this->assertEquals('testing', $app['env']);
        $this->assertInstanceOf('\Illuminate\Config\Repository', $app['config']);
    }

    /** @test */
    public function it_can_create_a_container()
    {
        $container = \Orchestra\Testbench\container();

        $app = $container->createApplication();

        $this->assertInstanceOf('\Illuminate\Foundation\Application', $app);
        $this->assertEquals('UTC', date_default_timezone_get());
        $this->assertEquals('testing', $app['env']);
        $this->assertInstanceOf('\Illuminate\Config\Repository', $app['config']);
    }
}
