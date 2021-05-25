<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\Foundation\Application;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    /**
     * @test
     * @group core
     */
    public function it_can_create_an_application()
    {
        $app = Application::create(realpath(__DIR__.'/../laravel'));

        $this->assertInstanceOf('Illuminate\Foundation\Application', $app);
    }
}
