<?php

namespace Orchestra\Testbench\Tests;

use PHPUnit\Framework\TestCase;

class TestCaseTest extends TestCase
{
    /** @test */
    public function it_can_create_the_testcase()
    {
        $testbench = new Stubs\TestCase();
        $app = $testbench->createApplication();

        $this->assertInstanceOf('\Orchestra\Testbench\Contracts\TestCase', $testbench);
        $this->assertInstanceOf('\Illuminate\Foundation\Application', $app);
        $this->assertEquals('UTC', date_default_timezone_get());
        $this->assertEquals('testing', $app['env']);
        $this->assertInstanceOf('\Illuminate\Config\Repository', $app['config']);
    }
}
