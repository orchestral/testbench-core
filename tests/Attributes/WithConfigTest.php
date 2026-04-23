<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Concerns\WithFixtures;
use Orchestra\Testbench\Tests\TestCase;

class WithConfigTest extends TestCase
{
    use WithFixtures;

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app)
    {
        return class_exists(WithConfigTest\WorkbenchServiceProvider::class, false)
            ? [WithConfigTest\WorkbenchServiceProvider::class]
            : [];
    }

    /** @test */
    #[WithConfig('testbench.attribute', true)]
    public function it_can_resolve_defined_configuration()
    {
        $this->assertSame(true, config('testbench.attribute'));
    }

    /**
     * @test
     *
     * @group without-parallel
     */
    #[WithConfig('testbench.session.attribute', true)]
    public function it_can_deferred_resolve_defined_configuration()
    {
        $this->assertSame(true, config('testbench.session.attribute'));
        $this->assertSame(false, config('testbench.session.report'));
        $this->assertSame(1, config('testbench.api'));
    }

    /**
     * @test
     *
     * @group without-parallel
     */
    #[WithConfig('testbench.session.attribute', true, defer: false)]
    public function it_can_eagerly_resolve_defined_configuration()
    {
        $this->assertSame(true, config('testbench.session.attribute'));
        $this->assertNull(config('testbench.session.report'));
        $this->assertSame(1, config('testbench.api'));
    }

    /** @test */
    public function it_does_not_persist_defined_configuration_between_tests()
    {
        $this->assertNull(config('testbench.attribute'));
    }
}
