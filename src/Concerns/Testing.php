<?php

namespace Orchestra\Testbench\Concerns;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Console\Application as Artisan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Queue\Queue;
use Illuminate\Support\Facades\ParallelTesting;
use Mockery;
use PHPUnit\Framework\TestCase;
use Throwable;

trait Testing
{
    use CreatesApplication,
        HandlesAnnotations,
        HandlesDatabases,
        HandlesRoutes,
        WithFactories,
        WithLaravelMigrations,
        WithLoadMigrationsFrom;

    /**
     * The Illuminate application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The callbacks that should be run after the application is created.
     *
     * @var array<int, callable():void>
     */
    protected $afterApplicationCreatedCallbacks = [];

    /**
     * The callbacks that should be run after the application is refreshed.
     *
     * @var array<int, callable():void>
     */
    protected $afterApplicationRefreshedCallbacks = [];

    /**
     * The callbacks that should be run before the application is destroyed.
     *
     * @var array<int, callable():void>
     */
    protected $beforeApplicationDestroyedCallbacks = [];

    /**
     * The exception thrown while running an application destruction callback.
     *
     * @var \Throwable
     */
    protected $callbackException;

    /**
     * Indicates if we have made it through the base setUp function.
     *
     * @var bool
     */
    protected $setUpHasRun = false;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    final protected function setUpTheTestEnvironment(): void
    {
        static::$latestResponse = null;

        if (! $this->app) {
            $this->refreshApplication();

            $this->setUpParallelTestingCallbacks();
        }

        foreach ($this->afterApplicationRefreshedCallbacks as $callback) {
            \call_user_func($callback);
        }

        $this->setUpTraits();

        foreach ($this->afterApplicationCreatedCallbacks as $callback) {
            \call_user_func($callback);
        }

        Model::setEventDispatcher($this->app['events']);

        $this->setUpApplicationRoutes();

        $this->setUpHasRun = true;
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    final protected function tearDownTheTestEnvironment(): void
    {
        if ($this->app) {
            $this->callBeforeApplicationDestroyedCallbacks();

            $this->tearDownParallelTestingCallbacks();

            $this->app->flush();

            $this->app = null;
        }

        $this->setUpHasRun = false;

        if (property_exists($this, 'serverVariables')) {
            $this->serverVariables = [];
        }

        if (property_exists($this, 'defaultHeaders')) {
            $this->defaultHeaders = [];
        }

        if (class_exists(Mockery::class)) {
            if ($container = Mockery::getContainer()) {
                $this->addToAssertionCount($container->mockery_getExpectationCount());
            }

            Mockery::close();
        }

        Carbon::setTestNow();

        if (class_exists(CarbonImmutable::class)) {
            CarbonImmutable::setTestNow();
        }

        $this->afterApplicationCreatedCallbacks = [];
        $this->beforeApplicationDestroyedCallbacks = [];

        if (property_exists($this, 'originalExceptionHandler')) {
            $this->originalExceptionHandler = null;
        }

        if (property_exists($this, 'originalDeprecationHandler')) {
            $this->originalDeprecationHandler = null;
        }

        Artisan::forgetBootstrappers();

        Queue::createPayloadUsing(null);

        HandleExceptions::forgetApp();

        if ($this->callbackException) {
            throw $this->callbackException;
        }
    }

    /**
     * Boot the testing helper traits.
     *
     * @param  array<class-string, class-string>  $uses
     * @return array<class-string, class-string>
     */
    final protected function setUpTheTestEnvironmentTraits(array $uses): array
    {
        $this->setUpDatabaseRequirements(function () use ($uses) {
            if (isset($uses[RefreshDatabase::class])) {
                $this->refreshDatabase();
            }

            if (isset($uses[DatabaseMigrations::class])) {
                $this->runDatabaseMigrations();
            }
        });

        if (isset($uses[DatabaseTransactions::class])) {
            $this->beginDatabaseTransaction();
        }

        if (isset($uses[WithoutMiddleware::class])) {
            $this->disableMiddlewareForAllTests();
        }

        if (isset($uses[WithoutEvents::class])) {
            $this->disableEventsForAllTests();
        }

        if (isset($uses[WithFaker::class])) {
            $this->setUpFaker();
        }

        return $uses;
    }

    /**
     * Setup parallel testing callback.
     */
    protected function setUpParallelTestingCallbacks(): void
    {
        if (class_exists(ParallelTesting::class) && $this instanceof TestCase) {
            ParallelTesting::callSetUpTestCaseCallbacks($this);
        }
    }

    /**
     * Teardown parallel testing callback.
     */
    protected function tearDownParallelTestingCallbacks(): void
    {
        if (class_exists(ParallelTesting::class) && $this instanceof TestCase) {
            ParallelTesting::callTearDownTestCaseCallbacks($this);
        }
    }

    /**
     * Register a callback to be run after the application is refreshed.
     *
     * @param  callable():void  $callback
     * @return void
     */
    protected function afterApplicationRefreshed(callable $callback): void
    {
        $this->afterApplicationRefreshedCallbacks[] = $callback;

        if ($this->setUpHasRun) {
            \call_user_func($callback);
        }
    }

    /**
     * Register a callback to be run after the application is created.
     *
     * @param  callable():void  $callback
     * @return void
     */
    protected function afterApplicationCreated(callable $callback): void
    {
        $this->afterApplicationCreatedCallbacks[] = $callback;

        if ($this->setUpHasRun) {
            \call_user_func($callback);
        }
    }

    /**
     * Register a callback to be run before the application is destroyed.
     *
     * @param  callable():void  $callback
     * @return void
     */
    protected function beforeApplicationDestroyed(callable $callback): void
    {
        array_unshift($this->beforeApplicationDestroyedCallbacks, $callback);
    }

    /**
     * Execute the application's pre-destruction callbacks.
     *
     * @return void
     */
    protected function callBeforeApplicationDestroyedCallbacks()
    {
        foreach ($this->beforeApplicationDestroyedCallbacks as $callback) {
            try {
                \call_user_func($callback);
            } catch (Throwable $e) {
                if (! $this->callbackException) {
                    $this->callbackException = $e;
                }
            }
        }
    }

    /**
     * Reload the application instance with cached routes.
     */
    protected function reloadApplication(): void
    {
        $this->tearDownTheTestEnvironment();
        $this->setUpTheTestEnvironment();
    }

    /**
     * This method is called when a test method did not execute successfully.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    protected function onNotSuccessfulTest(Throwable $exception): void
    {
        if (! $exception instanceof ExpectationFailedException || is_null(static::$latestResponse)) {
            parent::onNotSuccessfulTest($exception);
        }

        if ($lastException = static::$latestResponse->exceptions->last()) {
            parent::onNotSuccessfulTest($this->appendExceptionToException($lastException, $exception));

            return;
        }

        if (static::$latestResponse->baseResponse instanceof RedirectResponse) {
            $session = static::$latestResponse->baseResponse->getSession();

            if (! is_null($session) && $session->has('errors')) {
                parent::onNotSuccessfulTest($this->appendErrorsToException($session->get('errors')->all(), $exception));

                return;
            }
        }

        if (static::$latestResponse->baseResponse->headers->get('Content-Type') === 'application/json') {
            $testJson = new AssertableJsonString(static::$latestResponse->getContent());

            if (isset($testJson['errors'])) {
                parent::onNotSuccessfulTest($this->appendErrorsToException($testJson->json(), $exception, true));

                return;
            }
        }

        parent::onNotSuccessfulTest($exception);
    }

    /**
     * Append an exception to the message of another exception.
     *
     * @param  \Throwable  $exceptionToAppend
     * @param  \Throwable  $exception
     * @return \Throwable
     */
    protected function appendExceptionToException($exceptionToAppend, $exception)
    {
        $exceptionMessage = $exceptionToAppend->getMessage();

        $exceptionToAppend = (string) $exceptionToAppend;

        $message = <<<"EOF"
            The following exception occurred during the last request:
            $exceptionToAppend
            ----------------------------------------------------------------------------------
            $exceptionMessage
            EOF;

        return $this->appendMessageToException($message, $exception);
    }
    /**
     * Append errors to an exception message.
     *
     * @param  array  $errors
     * @param  \Throwable  $exception
     * @param  bool  $json
     * @return \Throwable
     */
    protected function appendErrorsToException($errors, $exception, $json = false)
    {
        $errors = $json
            ? json_encode($errors, JSON_PRETTY_PRINT)
            : implode(PHP_EOL, Arr::flatten($errors));

        // JSON error messages may already contain the errors, so we shouldn't duplicate them...
        if (str_contains($exception->getMessage(), $errors)) {
            return $exception;
        }

        $message = <<<"EOF"
            The following errors occurred during the last request:
            $errors
            EOF;

        return $this->appendMessageToException($message, $exception);
    }

    /**
     * Append a message to an exception.
     *
     * @param  string  $message
     * @param  \Throwable  $exception
     * @return \Throwable
     */
    protected function appendMessageToException($message, $exception)
    {
        $property = new ReflectionProperty($exception, 'message');

        $property->setAccessible(true);

        $property->setValue(
            $exception,
            $exception->getMessage().PHP_EOL.PHP_EOL.$message.PHP_EOL
        );

        return $exception;
    }

    /**
     * Boot the testing helper traits.
     *
     * @return array<class-string, class-string>
     */
    abstract protected function setUpTraits();

    /**
     * Refresh the application instance.
     *
     * @return void
     */
    abstract protected function refreshApplication();
}
