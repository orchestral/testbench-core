<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

class EncryptionTest extends TestCase
{
    #[Test]
    #[Group('phpunit-configuration')]
    public function it_can_encrypt_string()
    {
        $this->assertIsString(encrypt('laravel'));
    }
}
