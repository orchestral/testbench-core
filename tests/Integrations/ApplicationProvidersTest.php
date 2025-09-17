<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Orchestra\Testbench\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ApplicationProvidersTest extends TestCase
{
    #[Test]
    public function it_loaded_the_default_services()
    {
        $this->assertTrue($this->app->bound('blade.compiler'));
        $this->assertFalse($this->app->resolved('blade.compiler'));
    }
}
