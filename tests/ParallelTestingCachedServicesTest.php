<?php

namespace Orchestra\Testbench\Tests;

use LogicException;
use Orchestra\Sidekick\UndefinedValue;
use Orchestra\Testbench\TestCase;

use function Orchestra\Sidekick\Filesystem\join_paths;

/**
 * @group without-parallel
 */
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
            'APP_SERVICES_CACHE' => $_SERVER['APP_SERVICES_CACHE'] ?? new UndefinedValue,
        ];

        $_SERVER['LARAVEL_PARALLEL_TESTING'] = 1;
        $_SERVER['TEST_TOKEN'] = '9999';

        unset($_SERVER['APP_SERVICES_CACHE']);

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

        foreach (['LARAVEL_PARALLEL_TESTING', 'TEST_TOKEN', 'APP_SERVICES_CACHE'] as $key) {
            if ($this->originalParallelTestingState[$key] instanceof UndefinedValue) {
                unset($_SERVER[$key]);
            } else {
                $_SERVER[$key] = $this->originalParallelTestingState[$key];
            }
        }
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

        $this->assertArrayNotHasKey('APP_SERVICES_CACHE', $_SERVER);

        $this->setUpTheTestEnvironment();

        $this->assertFileExists($this->app->getCachedServicesPath());
    }

    /**
     * @test
     *
     * @dataProvider cachedServicesPaths
     */
    public function it_rejects_a_predefined_services_cache_in_parallel_testing(string $value)
    {
        $_SERVER['APP_SERVICES_CACHE'] = $value;

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
     * @dataProvider cachedServicesPaths
     */
    public function it_allows_a_predefined_services_cache_outside_parallel_testing(string $value)
    {
        unset($_SERVER['LARAVEL_PARALLEL_TESTING']);

        $_SERVER['APP_SERVICES_CACHE'] = $value;

        $this->resolveApplicationCachedServicesPathForParallelTesting();

        $this->assertSame($value, $_SERVER['APP_SERVICES_CACHE']);
        $this->assertSame($this->app->basePath($value), $this->app->getCachedServicesPath());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function cachedServicesPaths(): array
    {
        return [
            'custom' => ['bootstrap/cache/custom_services.php'],
            'empty' => [''],
        ];
    }
}
