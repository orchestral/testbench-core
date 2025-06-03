<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\Exceptions\ApplicationNotAvailableException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Runner\Version;

use function Orchestra\Testbench\laravel_or_fail;

class HelpersTest extends TestCase
{
    #[Test]
    public function it_can_compare_laravel_version()
    {
        $laravel = Application::VERSION === '12.x-dev' ? '12.0.0' : Application::VERSION;

        $this->assertSame(0, \Orchestra\Testbench\laravel_version_compare($laravel));
        $this->assertTrue(\Orchestra\Testbench\laravel_version_compare($laravel, '=='));
    }

    #[Test]
    public function it_can_compare_phpunit_version()
    {
        $version = Version::id();

        $phpunit = match (true) {
            str_starts_with($version, '12.2-') => '12.2.0',
            default => $version
        };

        $this->assertSame(0, \Orchestra\Testbench\phpunit_version_compare($phpunit));
        $this->assertTrue(\Orchestra\Testbench\phpunit_version_compare($phpunit, '=='));
    }

    #[Test]
    public function it_can_throw_application_not_available_application_when_app_is_not_laravel()
    {
        $this->expectException(ApplicationNotAvailableException::class);
        $this->expectExceptionMessage(\sprintf('Application is not available to run [%s]', __METHOD__));

        laravel_or_fail(null);
    }
}
