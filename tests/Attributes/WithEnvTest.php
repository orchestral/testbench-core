<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Illuminate\Foundation\Application;
use Mockery as m;
use Orchestra\Testbench\Attributes\WithEnv;
use Orchestra\Testbench\Foundation\Env;
use PHPUnit\Framework\TestCase;

class WithEnvTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

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
        $_ENV['LARAVEL_KEY'] = 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF';

        $attribute = new WithEnv('LARAVEL_KEY', 'laravel');

        $this->assertSame('AckfSECXIvnK5r28GVIWUAxmbBSjTsmF', Env::get('LARAVEL_KEY'));

        $callback = $attribute(m::mock(Application::class));

        $this->assertSame('AckfSECXIvnK5r28GVIWUAxmbBSjTsmF', Env::get('LARAVEL_KEY'));

        value($callback);

        $this->assertSame('AckfSECXIvnK5r28GVIWUAxmbBSjTsmF', Env::get('LARAVEL_KEY'));

        unset($_ENV['LARAVEL_KEY']);
    }
}
