<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Orchestra\Testbench\Attributes\UsesVendor;
use Orchestra\Testbench\TestCase;

class UsesVendorTest extends TestCase
{
    /** @test */
    #[UsesVendor]
    public function it_can_uses_vendor_attribute()
    {
        $this->assertTrue(is_link(base_path('vendor')));
    }
}
