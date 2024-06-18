<?php

namespace Orchestra\Testbench\Tests\Foundation;

use Orchestra\Testbench\Foundation\UndefinedValue;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UndefinedValueTest extends TestCase
{
    #[Test]
    public function it_can_be_resolved()
    {
        $stub = new UndefinedValue();

        $this->assertInstanceOf(UndefinedValue::class, $stub);
        $this->assertTrue(UndefinedValue::equalsTo($stub));
        $this->assertTrue(UndefinedValue::equalsTo(null));
        $this->assertFalse(UndefinedValue::equalsTo('Testbench'));
        $this->assertFalse(UndefinedValue::equalsTo(''));
    }

    #[Test]
    public function it_can_be_serialized()
    {
        $stub = new UndefinedValue();

        $this->assertNull($stub->jsonSerialize());
        $this->assertSame('{"content":null}', json_encode(['content' => $stub], true));
    }
}
