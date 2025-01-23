<?php

namespace Orchestra\Testbench\Tests\PHPUnit;

use Orchestra\Testbench\PHPUnit\AttributeParser;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AttributeParserTest extends TestCase
{
    #[Test]
    public function it_can_validate_attribute()
    {
        $this->assertFalse(AttributeParser::validAttribute('TestCase::class'));
        $this->assertFalse(AttributeParser::validAttribute(TestCase::class));
        $this->assertFalse(AttributeParser::validAttribute('Orchestra\Testbench\Support\FluentDecorator'));

        $this->assertTrue(AttributeParser::validAttribute('Orchestra\Testbench\Attributes\Define'));
    }
}
