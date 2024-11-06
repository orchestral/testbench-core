<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\TestCase;

#[WithConfig('app.key', 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF')]
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
