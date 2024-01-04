<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Illuminate\Foundation\Application;
use Mockery as m;
use Orchestra\Testbench\Attributes\WithEnv;
use Orchestra\Testbench\Foundation\Env;
use Orchestra\Testbench\TestCase;

class WithEnvTest extends TestCase
{
    /** @test */
    public function it_can_resolve_defined_env_variables()
    {
        $attribute = new WithEnv('TESTING_USING_ATTRIBUTE', '(true)');

        $this->assertNull(Env::get('TESTING_USING_ATTRIBUTE'));

        $callback = $attribute(m::mock(Application::class));

        $this->assertTrue(Env::get('TESTING_USING_ATTRIBUTE'));

        value($callback);

        $this->assertNull(Env::get('TESTING_USING_ATTRIBUTE'));
    }

    /** @test */
    public function it_does_not_persist_defined_env_variables_between_tests()
    {
        $this->assertNull(Env::get('TESTING_USING_ATTRIBUTE'));
    }

    /**
     * @test
     */
    public function it_cannot_change_defined_env_variables()
    {
        $attribute = new WithEnv('APP_KEY', 'laravel');

        $this->assertSame('AckfSECXIvnK5r28GVIWUAxmbBSjTsmF', Env::get('APP_KEY'));

        $callback = $attribute(m::mock(Application::class));

        $this->assertSame('AckfSECXIvnK5r28GVIWUAxmbBSjTsmF', Env::get('APP_KEY'));

        value($callback);

        $this->assertSame('AckfSECXIvnK5r28GVIWUAxmbBSjTsmF', Env::get('APP_KEY'));
    }
}
