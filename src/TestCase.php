<?php

namespace Orchestra\Testbench;

use Illuminate\Foundation\Testing;
use PHPUnit\Framework\TestCase as PHPUnit;

abstract class TestCase extends PHPUnit implements Contracts\TestCase
{
    use Concerns\Testing,
        Testing\Concerns\InteractsWithAuthentication,
        Testing\Concerns\InteractsWithConsole,
        Testing\Concerns\InteractsWithContainer,
        Testing\Concerns\InteractsWithDatabase,
        Testing\Concerns\InteractsWithExceptionHandling,
        Testing\Concerns\InteractsWithSession,
        Testing\Concerns\InteractsWithTime,
        Testing\Concerns\MakesHttpRequests;

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Automatically loads environment file if available.
     *
     * @var bool
     */
    protected $loadEnvironmentVariables = true;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->setUpTheTestEnvironment();
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->tearDownTheTestEnvironment();
    }

    /**
     * Boot the testing helper traits.
     *
     * @return array
     */
    protected function setUpTraits()
    {
        $uses = \array_flip(\class_uses_recursive(static::class));

        return $this->setUpTheTestEnvironmentTraits($uses);
    }

    /**
     * Refresh the application instance.
     *
     * @return void
     */
    protected function refreshApplication()
    {
        $this->app = $this->createApplication();
    }
}
