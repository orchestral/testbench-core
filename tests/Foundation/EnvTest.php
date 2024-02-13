<?php

namespace Orchestra\Testbench\Tests\Foundation;

use Orchestra\Testbench\Foundation\Env;
use Orchestra\Testbench\TestCase;

/**
 * @group phpunit-configuration
 */
class EnvTest extends TestCase
{
    /** @test */
    public function it_can_determined_has_env_values()
    {
        $_ENV['TESTING_TRUE_EXAMPLE'] = true;
        $_ENV['TESTING_FALSE_EXAMPLE'] = false;
        $_ENV['TESTING_EMPTY_EXAMPLE'] = '';

        $this->assertTrue(Env::has('APP_KEY'));
        $this->assertFalse(Env::has('ASSET_URL'));
        $this->assertFalse(Env::has('LOG_DEPRECATIONS_CHANNEL'));
        $this->assertTrue(Env::has('TESTING_TRUE_EXAMPLE'));
        $this->assertTrue(Env::has('TESTING_FALSE_EXAMPLE'));
        $this->assertTrue(Env::has('TESTING_EMPTY_EXAMPLE'));

        unset(
            $_ENV['TESTING_TRUE_EXAMPLE'],
            $_ENV['TESTING_FALSE_EXAMPLE'],
            $_ENV['TESTING_EMPTY_EXAMPLE']
        );
    }
    /** @test */
    public function it_can_correctly_forward_env_values()
    {
        $_ENV['TESTING_TRUE_EXAMPLE'] = true;
        $_ENV['TESTING_FALSE_EXAMPLE'] = false;
        $_ENV['TESTING_EMPTY_EXAMPLE'] = '';

        $this->assertSame('AckfSECXIvnK5r28GVIWUAxmbBSjTsmF', Env::forward('APP_KEY'));
        $this->assertSame('(null)', Env::forward('ASSET_URL'));
        $this->assertSame('(null)', Env::forward('LOG_DEPRECATIONS_CHANNEL'));
        $this->assertSame('(true)', Env::forward('TESTING_TRUE_EXAMPLE'));
        $this->assertSame('(false)', Env::forward('TESTING_FALSE_EXAMPLE'));
        $this->assertSame('(empty)', Env::forward('TESTING_EMPTY_EXAMPLE'));

        unset(
            $_ENV['TESTING_TRUE_EXAMPLE'],
            $_ENV['TESTING_FALSE_EXAMPLE'],
            $_ENV['TESTING_EMPTY_EXAMPLE']
        );
    }
}
