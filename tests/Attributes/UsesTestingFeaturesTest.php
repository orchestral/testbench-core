<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\TestCase;

abstract class BaseTestCase extends TestCase
{
    /**
     * @beforeClass
     */
    public static function defineTestingFeatures()
    {
        static::usesTestingFeature(new WithConfig('fake.parent_attribute', true));
        static::usesTestingFeature(new WithConfig('fake.override_attribute', 'parent'));
    }
}

#[WithConfig('fake.override_attribute', 'child')]
class UsesTestingFeaturesTest extends BaseTestCase
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
