<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Orchestra\Testbench\Attributes\WithEnv;
use Orchestra\Testbench\Foundation\Env;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class WithEnvTest extends TestCase
{
    #[Test]
    #[WithEnv('TESTING_USING_ATTRIBUTE', '(true)')]
    public function it_can_resolve_defined_env_variables()
    {
        $this->assertSame(true, Env::get('TESTING_USING_ATTRIBUTE'));
    }

    #[Test]
    public function it_does_not_persist_defined_env_variables_between_tests()
    {
        $this->assertNull(Env::get('TESTING_USING_ATTRIBUTE'));
    }
}
