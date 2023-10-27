<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\LazyCollection;

trait Testing
{
    use ApplicationTestingHooks;
    use CreatesApplication;
    use HandlesAnnotations;
    use HandlesAttributes;
    use HandlesDatabases;
    use HandlesRoutes;
    use InteractsWithMigrations;
    use WithFactories;

    /**
     * The Illuminate application instance.
     *
     * @var \Illuminate\Foundation\Application|null
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
     * @var \Throwable|null
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
     * @internal
     *
     * @return void
     */
    final protected function setUpTheTestEnvironment(): void
    {
        $this->setUpTheApplicationTestingHooks(function () {
            $this->setUpTraits();
        });

        $this->setUpHasRun = true;
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @internal
     *
     * @return void
     */
    final protected function tearDownTheTestEnvironment(): void
    {
        $this->tearDownTheApplicationTestingHooks(function () {
            $this->setUpHasRun = false;

            if (property_exists($this, 'serverVariables')) {
                $this->serverVariables = [];
            }

            if (property_exists($this, 'defaultHeaders')) {
                $this->defaultHeaders = [];
            }

            if (property_exists($this, 'originalExceptionHandler')) {
                $this->originalExceptionHandler = null;
            }

            if (property_exists($this, 'originalDeprecationHandler')) {
                $this->originalDeprecationHandler = null;
            }
        });
    }

    /**
     * Boot the testing helper traits.
     *
     * @internal
     *
     * @param  array<class-string, class-string>  $uses
     * @return array<class-string, class-string>
     */
    final protected function setUpTheTestEnvironmentTraits(array $uses): array
    {
        if (isset($uses[WithWorkbench::class])) {
            /** @phpstan-ignore-next-line */
            $this->setUpWithWorkbench();
        }

        $this->setUpDatabaseRequirements(function () use ($uses) {
            if (isset($uses[RefreshDatabase::class])) {
                /** @phpstan-ignore-next-line */
                $this->refreshDatabase();
            }

            if (isset($uses[DatabaseMigrations::class])) {
                /** @phpstan-ignore-next-line */
                $this->runDatabaseMigrations();
            }

            if (isset($uses[DatabaseTruncation::class])) {
                /** @phpstan-ignore-next-line */
                $this->truncateDatabaseTables();
            }
        });

        if (isset($uses[DatabaseTransactions::class])) {
            /** @phpstan-ignore-next-line */
            $this->beginDatabaseTransaction();
        }

        if (isset($uses[WithoutMiddleware::class])) {
            /** @phpstan-ignore-next-line */
            $this->disableMiddlewareForAllTests();
        }

        if (isset($uses[WithoutEvents::class])) {
            /** @phpstan-ignore-next-line */
            $this->disableEventsForAllTests();
        }

        if (isset($uses[WithFaker::class])) {
            /** @phpstan-ignore-next-line */
            $this->setUpFaker();
        }

        LazyCollection::make(static function () use ($uses) {
            foreach ($uses as $use) {
                yield $use;
            }
        })
            ->reject(function ($use) {
                /** @var class-string $use */
                return $this->setUpTheTestEnvironmentTraitToBeIgnored($use);
            })->map(static function ($use) {
                /** @var class-string $use */
                return class_basename($use);
            })->each(function ($traitBaseName) {
                /** @var string $traitBaseName */
                if (method_exists($this, $method = 'setUp'.$traitBaseName)) {
                    $this->{$method}();
                }

                if (method_exists($this, $method = 'tearDown'.$traitBaseName)) {
                    $this->beforeApplicationDestroyed(function () use ($method) {
                        $this->{$method}();
                    });
                }
            });

        return $uses;
    }

    /**
     * Determine trait should be ignored from being autoloaded.
     *
     * @param  class-string  $use
     * @return bool
     */
    protected function setUpTheTestEnvironmentTraitToBeIgnored(string $use): bool
    {
        return false;
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
     * Boot the testing helper traits.
     *
     * @return array<class-string, class-string>
     */
    abstract protected function setUpTraits();
}
