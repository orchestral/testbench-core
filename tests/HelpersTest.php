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
        $this->assertSame(0, \Orchestra\Testbench\laravel_version_compare(Application::VERSION));
        $this->assertTrue(\Orchestra\Testbench\laravel_version_compare(Application::VERSION, '=='));
    }

    #[Test]
    public function it_can_compare_phpunit_version()
    {
        $this->assertSame(0, \Orchestra\Testbench\phpunit_version_compare(Version::id()));
        $this->assertTrue(\Orchestra\Testbench\phpunit_version_compare(Version::id(), '=='));
    }

    #[Test]
    public function it_can_throw_application_not_available_application_when_app_is_not_laravel()
    {
        $this->expectException(ApplicationNotAvailableException::class);
        $this->expectExceptionMessage(\sprintf('Application is not available to run [%s]', __METHOD__));

        laravel_or_fail(null);
    }
}
