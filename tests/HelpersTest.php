<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\Exceptions\ApplicationNotAvailableException;
use PHPUnit\Framework\Attributes\Test;

use function Orchestra\Testbench\laravel_or_fail;

class HelpersTest extends TestCase
{
    #[Test]
    public function it_can_compare_laravel_version()
    {
        $laravelVersion = \Orchestra\Sidekick\laravel_normalize_version();

        $this->assertSame(0, \Orchestra\Testbench\laravel_version_compare($laravelVersion));
        $this->assertTrue(\Orchestra\Testbench\laravel_version_compare($laravelVersion, '=='));
    }

    #[Test]
    public function it_can_compare_php_version()
    {
        $phpVersion = \Orchestra\Sidekick\php_normalize_version();

        $this->assertSame(0, \Orchestra\Testbench\php_version_compare($phpVersion));
        $this->assertTrue(\Orchestra\Testbench\php_version_compare($phpVersion, '=='));
    }

    #[Test]
    public function it_can_compare_phpunit_version()
    {
        $phpunitVersion = \Orchestra\Sidekick\phpunit_normalize_version();

        $this->assertSame(0, \Orchestra\Testbench\phpunit_version_compare($phpunitVersion));
        $this->assertTrue(\Orchestra\Testbench\phpunit_version_compare($phpunitVersion, '=='));
    }

    #[Test]
    public function it_can_throw_application_not_available_application_when_app_is_not_laravel()
    {
        $this->expectException(ApplicationNotAvailableException::class);
        $this->expectExceptionMessage(\sprintf('Application is not available to run [%s]', __METHOD__));

        laravel_or_fail(null);
    }
}
