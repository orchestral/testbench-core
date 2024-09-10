<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\TestCase;


#[WithConfig('fake.parent_attribute', true)]
#[WithConfig('fake.override_attribute', 'parent')]
abstract class ParentTest extends TestCase {}

#[WithConfig('fake.override_attribute', 'child')]
class InheritanceTest extends ParentTest
{
    /** @test */
    public function it_can_see_parent_attributes() {
        $this->assertSame(true, config('fake.parent_attribute'));
    }

    /** @test */
    public function it_can_override_parent_attributes() {
        $this->assertSame('child', config('fake.override_attribute'));
    }
}
