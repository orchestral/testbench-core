<?php

namespace Orchestra\Testbench\Tests\Helpers;

use Orchestra\Testbench\TestCase;

use function Orchestra\Testbench\once;

class OnceTest extends TestCase
{
    /** @test */
    public function it_can_resolve_callback_only_once()
    {
        $stub = once(function () {
            $this->app->instance(__CLASS__.'.once', now());
        });
        $stub2 = once(function () {
            $this->app->instance(__CLASS__.'.once2', now());
        });

        $this->assertFalse($this->app->bound(__CLASS__.'.once'));
        $this->assertFalse($this->app->bound(__CLASS__.'.once2'));

        value($stub);

        $this->assertTrue($this->app->bound(__CLASS__.'.once'));
        $this->assertFalse($this->app->bound(__CLASS__.'.once2'));

        tap($this->app[__CLASS__.'.once'], function ($time) use ($stub) {
            value($stub);
            $this->assertSame($time, $this->app[__CLASS__.'.once']);
        });

        value($stub2);

        $this->assertTrue($this->app->bound(__CLASS__.'.once'));
        $this->assertTrue($this->app->bound(__CLASS__.'.once2'));
    }
}
