<?php

namespace Orchestra\Testbench\Tests\Support;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\Support\FluentDecorator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FluentDecoratorTest extends TestCase
{
    #[Test]
    public function it_can_utilise_fluent_features()
    {
        [$fluent, $attributes] = $this->newFluent();

        $this->assertTrue(isset($fluent['testbench']));
        $this->assertTrue(isset($fluent['class']));
        $this->assertFalse(isset($fluent['workbench']));

        $this->assertTrue($fluent['testbench']);
        $this->assertNull($fluent['workbench']);

        $this->assertSame($attributes, $fluent->getAttributes());
        $this->assertSame($attributes, $fluent->toArray());
        $this->assertSame(json_encode($attributes), $fluent->toJson());
        $this->assertSame($attributes, $fluent->jsonSerialize());
    }

    #[Test]
    public function it_can_utilise_fluent_as_object()
    {
        [$fluent] = $this->newFluent();

        $this->assertFalse(isset($fluent->laravel));
        $this->assertFalse(isset($fluent->file));
        $this->assertNull($fluent->laravel);
        $this->assertNull($fluent->file);

        $fluent->laravel = Application::VERSION;
        $fluent->file = __FILE__;

        $this->assertTrue(isset($fluent->laravel));
        $this->assertTrue(isset($fluent->file));
        $this->assertSame(Application::VERSION, $fluent->laravel);
        $this->assertSame(__FILE__, $fluent->file);

        unset($fluent->file);

        $this->assertTrue(isset($fluent->laravel));
        $this->assertFalse(isset($fluent->file));
    }

    #[Test]
    public function it_can_utilise_fluent_as_array()
    {
        [$fluent] = $this->newFluent();

        $this->assertFalse(isset($fluent['laravel']));
        $this->assertFalse(isset($fluent['file']));
        $this->assertNull($fluent['laravel']);
        $this->assertNull($fluent['file']);

        $fluent['laravel'] = Application::VERSION;
        $fluent['file'] = __FILE__;

        $this->assertTrue(isset($fluent['laravel']));
        $this->assertTrue(isset($fluent['file']));
        $this->assertSame(Application::VERSION, $fluent['laravel']);
        $this->assertSame(__FILE__, $fluent['file']);

        unset($fluent['file']);

        $this->assertTrue(isset($fluent['laravel']));
        $this->assertFalse(isset($fluent['file']));
    }

    #[Test]
    public function it_can_set_fluent_attribute_using_method_call()
    {
        [$fluent] = $this->newFluent();

        $this->assertFalse(isset($fluent['laravel']));
        $this->assertNull($fluent['laravel']);

        $this->assertInstanceOf(FluentDecorator::class, $fluent->laravel(Application::VERSION));

        $this->assertTrue(isset($fluent['laravel']));
        $this->assertSame(Application::VERSION, $fluent['laravel']);
    }

    /**
     * Create new test stubs.
     *
     * @return array{0: \Orchestra\Testbench\Support\FluentDecorator, 1: array<array-key, mixed>}
     */
    protected function newFluent(): array
    {
        $attributes = ['testbench' => true, 'class' => __CLASS__];

        return [
            new class($attributes) extends FluentDecorator
            {
                // ...
            },
            $attributes,
        ];
    }
}
