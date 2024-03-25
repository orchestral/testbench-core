<?php

namespace Orchestra\Testbench;

use Illuminate\Foundation\Testing;
use Throwable;

abstract class TestCase extends PHPUnit\TestCase implements Contracts\TestCase
{
    use Concerns\Testing;
    use Testing\Concerns\InteractsWithAuthentication;
    use Testing\Concerns\InteractsWithConsole;
    use Testing\Concerns\InteractsWithContainer;
    use Testing\Concerns\InteractsWithDatabase;
    use Testing\Concerns\InteractsWithDeprecationHandling;
    use Testing\Concerns\InteractsWithExceptionHandling;
    use Testing\Concerns\InteractsWithSession;
    use Testing\Concerns\InteractsWithTime;
    use Testing\Concerns\MakesHttpRequests;

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
     * Automatically enables package discoveries.
     *
     * @var bool
     */
    protected $enablesPackageDiscoveries = false;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        static::$latestResponse = null;

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
     * @return array<class-string, class-string>
     */
    protected function setUpTraits()
    {
        return $this->setUpTheTestEnvironmentTraits(static::cachedUsesForTestCase());
    }

    /**
     * Determine trait should be ignored from being autoloaded.
     *
     * @param  class-string  $use
     * @return bool
     */
    protected function setUpTheTestEnvironmentTraitToBeIgnored(string $use): bool
    {
        return \in_array($use, [
            Testing\RefreshDatabase::class,
            Testing\DatabaseMigrations::class,
            Testing\DatabaseTransactions::class,
            Testing\WithoutMiddleware::class,
            Testing\WithoutEvents::class,
            Testing\WithFaker::class,
            Testing\Concerns\InteractsWithAuthentication::class,
            Testing\Concerns\InteractsWithConsole::class,
            Testing\Concerns\InteractsWithContainer::class,
            Testing\Concerns\InteractsWithDatabase::class,
            Testing\Concerns\InteractsWithDeprecationHandling::class,
            Testing\Concerns\InteractsWithExceptionHandling::class,
            Testing\Concerns\InteractsWithSession::class,
            Testing\Concerns\InteractsWithTime::class,
            Testing\Concerns\MakesHttpRequests::class,
            Concerns\ApplicationTestingHooks::class,
            Concerns\CreatesApplication::class,
            Concerns\Database\HandlesConnections::class,
            Concerns\HandlesAnnotations::class,
            Concerns\HandlesDatabases::class,
            Concerns\HandlesRoutes::class,
            Concerns\InteractsWithPHPUnit::class,
            Concerns\InteractsWithTestCase::class,
            Concerns\InteractsWithWorkbench::class,
            Concerns\Testing::class,
            Concerns\WithFactories::class,
            Concerns\WithWorkbench::class,
        ]);
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

    /**
     * Prepare the testing environment before the running the test case.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public static function setUpBeforeClass(): void
    {
        static::setUpBeforeClassUsingPHPUnit();
        static::setUpBeforeClassUsingTestCase();
        static::setUpBeforeClassUsingWorkbench();
    }

    /**
     * Clean up the testing environment before the next test case.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public static function tearDownAfterClass(): void
    {
        static::tearDownAfterClassUsingWorkbench();
        static::tearDownAfterClassUsingTestCase();
        static::tearDownAfterClassUsingPHPUnit();
    }

    /**
     * This method is called when a test method did not execute successfully.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    protected function onNotSuccessfulTest(Throwable $exception): void
    {
        parent::onNotSuccessfulTest(
            ! \is_null(static::$latestResponse)
                ? static::$latestResponse->transformNotSuccessfulException($exception)
                : $exception
        );
    }
}
