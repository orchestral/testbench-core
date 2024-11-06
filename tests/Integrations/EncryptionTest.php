<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[WithConfig('app.key', 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF')]
class EncryptionTest extends TestCase
{
    #[Test]
    #[Group('phpunit-configuration')]
    public function it_can_encrypt_string()
    {
        $this->assertIsString(encrypt('laravel'));
    }
}
