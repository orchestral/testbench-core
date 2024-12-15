<?php

namespace Orchestra\Testbench\Tests\Support;

use PHPUnit\Framework\TestCase;
use Orchestra\Testbench\Support\FluentDecorator;

class FluentDecoratorTest extends TestCase
{
    /** @test */
    public function it_can_be_utilise_fluent_features()
    {
        $fluent = new class($attributes = [
            'testbench' => true,
            'class' => __CLASS__,
        ]) extends FluentDecorator {
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
    }
}
