<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Orchestra\Testbench\TestCase;

class EncryptionTest extends TestCase
{
    /**
     * @test
     *
     * @group phpunit-configuration
     */
    public function it_can_encrypt_string()
    {
        $this->assertIsString(encrypt('laravel'));
    }
}
