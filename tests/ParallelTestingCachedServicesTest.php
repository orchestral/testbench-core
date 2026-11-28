<?php

namespace Orchestra\Testbench\Tests;

use LogicException;
use Orchestra\Sidekick\UndefinedValue;
use Orchestra\Testbench\Foundation\Env;
use Orchestra\Testbench\TestCase;

use function Orchestra\Sidekick\Filesystem\join_paths;

class ParallelTestingCachedServicesTest extends TestCase
{
    /**
     * The original parallel testing state.
     *
     * @var array<string, mixed>
     */
    protected array $originalParallelTestingState = [];

    /** {@inheritDoc} */
    #[\Override]
    protected function setUp(): void
    {
        $this->originalParallelTestingState = [
            'LARAVEL_PARALLEL_TESTING' => $_SERVER['LARAVEL_PARALLEL_TESTING'] ?? new UndefinedValue,
            'TEST_TOKEN' => $_SERVER['TEST_TOKEN'] ?? new UndefinedValue,
            'APP_SERVICES_CACHE' => [
                'server' => $_SERVER['APP_SERVICES_CACHE'] ?? new UndefinedValue,
                'env' => $_ENV['APP_SERVICES_CACHE'] ?? new UndefinedValue,
                'putenv' => getenv('APP_SERVICES_CACHE'),
            ],
        ];

        $_SERVER['LARAVEL_PARALLEL_TESTING'] = 1;
        $_SERVER['TEST_TOKEN'] = '9999';

        $this->forgetCachedServicesEnvironmentVariable();

        parent::setUp();
    }

    /** {@inheritDoc} */
    #[\Override]
    protected function tearDown(): void
    {
        $cachePath = $this->app->bootstrapPath('cache');

        parent::tearDown();

        foreach (['test_9999_services.php', 'test_9999_packages.php'] as $file) {
            if (is_file(join_paths($cachePath, $file))) {
                unlink(join_paths($cachePath, $file));
            }
        }

        foreach (['LARAVEL_PARALLEL_TESTING', 'TEST_TOKEN'] as $key) {
            if ($this->originalParallelTestingState[$key] instanceof UndefinedValue) {
                unset($_SERVER[$key]);
            } else {
                $_SERVER[$key] = $this->originalParallelTestingState[$key];
            }
        }

        $this->forgetCachedServicesEnvironmentVariable();

        ['server' => $server, 'env' => $env, 'putenv' => $putenv] = $this->originalParallelTestingState['APP_SERVICES_CACHE'];

        if (! $server instanceof UndefinedValue) {
            $_SERVER['APP_SERVICES_CACHE'] = $server;
        }

        if (! $env instanceof UndefinedValue) {
            $_ENV['APP_SERVICES_CACHE'] = $env;
        }

        if ($putenv !== false) {
            putenv("APP_SERVICES_CACHE={$putenv}");
        }
    }

    /**
     * Forget "APP_SERVICES_CACHE" from every environment source.
     *
     * The environment repository is immutable, so it can't clear values it didn't set itself
     * (e.g. one set by an earlier test in the same parallel worker before the repository was flushed).
     */
    protected function forgetCachedServicesEnvironmentVariable(): void
    {
        Env::forget('APP_SERVICES_CACHE');

        unset($_SERVER['APP_SERVICES_CACHE'], $_ENV['APP_SERVICES_CACHE']);
        putenv('APP_SERVICES_CACHE');
    }

    /** @test */
    public function it_uses_a_cached_services_manifest_for_each_parallel_worker()
    {
        $this->assertSame(
            $this->app->basePath(join_paths('bootstrap', 'cache', 'test_9999_services.php')),
            $this->app->getCachedServicesPath()
        );

        $this->assertFileExists($this->app->getCachedServicesPath());
    }

    /** @test */
    public function it_reuses_the_worker_services_cache_when_refreshing_the_application()
    {
        $cachePath = $this->app->getCachedServicesPath();

        $this->refreshApplication();

        $this->assertSame($cachePath, $this->app->getCachedServicesPath());
    }

    /** @test */
    public function it_clears_the_worker_services_cache_environment_after_the_test()
    {
        $this->tearDownTheTestEnvironment();

        $this->assertFalse(Env::has('APP_SERVICES_CACHE'));

        $this->setUpTheTestEnvironment();

        $this->assertFileExists($this->app->getCachedServicesPath());
    }

    /**
     * @test
     *
     * @dataProvider cachedServicesEnvironmentSources
     */
    public function it_rejects_a_predefined_services_cache_in_parallel_testing(string $source, string $value)
    {
        $this->setCachedServicesEnvironmentVariable($source, $value);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('APP_SERVICES_CACHE cannot be defined when running tests in parallel.');

        try {
            $this->createApplication();
        } finally {
            $this->resolveApplicationFacades($this->app);
        }
    }

    /**
     * @test
     *
     * @dataProvider cachedServicesEnvironmentSources
     */
    public function it_allows_a_predefined_services_cache_outside_parallel_testing(string $source, string $value)
    {
        unset($_SERVER['LARAVEL_PARALLEL_TESTING']);

        $this->setCachedServicesEnvironmentVariable($source, $value);

        $this->resolveApplicationCachedServicesPathForParallelTesting();

        $this->assertSame($value, Env::get('APP_SERVICES_CACHE'));
        $this->assertSame($this->app->basePath($value), $this->app->getCachedServicesPath());
    }

    /**
     * @test
     *
     * @dataProvider parallelTestingStates
     */
    public function it_ignores_a_process_only_services_cache_when_putenv_is_disabled(bool $parallel)
    {
        if (! $parallel) {
            unset($_SERVER['LARAVEL_PARALLEL_TESTING']);
        }

        $this->setCachedServicesEnvironmentVariable('putenv', 'bootstrap/cache/custom_services.php');

        $this->assertFalse(Env::has('APP_SERVICES_CACHE'));

        $this->resolveApplicationCachedServicesPathForParallelTesting();

        $this->assertSame(
            $this->app->basePath(join_paths('bootstrap', 'cache', $parallel ? 'test_9999_services.php' : 'services.php')),
            $this->app->getCachedServicesPath()
        );
    }

    /**
     * @test
     *
     * @dataProvider parallelTestingStates
     */
    public function it_handles_a_process_only_services_cache_when_putenv_is_enabled(bool $parallel)
    {
        if (! $parallel) {
            unset($_SERVER['LARAVEL_PARALLEL_TESTING']);
        }

        $value = 'bootstrap/cache/custom_services.php';
        $this->setCachedServicesEnvironmentVariable('putenv', $value);
        Env::enablePutenv();

        try {
            if ($parallel) {
                $this->expectException(LogicException::class);
                $this->expectExceptionMessage('APP_SERVICES_CACHE cannot be defined when running tests in parallel.');
            }

            $this->resolveApplicationCachedServicesPathForParallelTesting();

            $this->assertSame($value, Env::get('APP_SERVICES_CACHE'));
            $this->assertSame($this->app->basePath($value), $this->app->getCachedServicesPath());
        } finally {
            Env::disablePutenv();
        }
    }

    /**
     * @return array<string, array{bool}>
     */
    public static function parallelTestingStates(): array
    {
        return [
            'parallel' => [true],
            'serial' => [false],
        ];
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function cachedServicesEnvironmentSources(): array
    {
        return [
            'server' => ['server', 'bootstrap/cache/custom_services.php'],
            'env' => ['env', 'bootstrap/cache/custom_services.php'],
            'empty' => ['server', ''],
        ];
    }

    /**
     * Set "APP_SERVICES_CACHE" in the given environment source.
     */
    protected function setCachedServicesEnvironmentVariable(string $source, string $value): void
    {
        $this->forgetCachedServicesEnvironmentVariable();

        if ($source === 'server') {
            $_SERVER['APP_SERVICES_CACHE'] = $value;
        } elseif ($source === 'env') {
            $_ENV['APP_SERVICES_CACHE'] = $value;
        } else {
            putenv("APP_SERVICES_CACHE={$value}");
        }
    }
}
