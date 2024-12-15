<?php

namespace Orchestra\Testbench\Tests\Support;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\Support\FluentDecorator;
use PHPUnit\Framework\TestCase;

class FluentDecoratorTest extends TestCase
{
    /** @test */
    public function it_can_be_utilise_fluent_features()
    {
        $fluent = new class($attributes = ['testbench' => true, 'class' => __CLASS__]) extends FluentDecorator
        {
            // ...
        };

        $this->assertTrue(isset($fluent['testbench']));
        $this->assertFalse(isset($fluent['workbench']));

        $this->assertTrue($fluent['testbench']);
        $this->assertNull($fluent['workbench']);

        $this->assertSame($attributes, $fluent->getAttributes());
        $this->assertSame($attributes, $fluent->toArray());
        $this->assertSame(json_encode($attributes), $fluent->toJson());
        $this->assertSame($attributes, $fluent->jsonSerialize());

        $this->assertFalse(isset($fluent['laravel']));
        $this->assertFalse(isset($fluent->laravel));
        $this->assertNull($fluent['laravel']);
        $this->assertNull($fluent->laravel);

        $this->assertInstanceOf(FluentDecorator::class, $fluent->laravel(Application::VERSION));

        $this->assertTrue(isset($fluent['laravel']));
        $this->assertTrue(isset($fluent->laravel));
        $this->assertSame(Application::VERSION, $fluent['laravel']);
        $this->assertSame(Application::VERSION, $fluent->laravel);

        unset($fluent['class']);

        $this->assertFalse(isset($fluent['class']));
        $this->assertFalse(isset($fluent->class));
        $this->assertNull($fluent['class']);
        $this->assertNull($fluent->class);
    }
}
