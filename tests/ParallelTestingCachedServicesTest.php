<?php

namespace Orchestra\Testbench\Tests;

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
            'APP_SERVICES_CACHE' => Env::get('APP_SERVICES_CACHE', new UndefinedValue),
        ];

        $_SERVER['LARAVEL_PARALLEL_TESTING'] = 1;
        $_SERVER['TEST_TOKEN'] = '9999';

        Env::forget('APP_SERVICES_CACHE');

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

        Env::forget('APP_SERVICES_CACHE');

        if (! $this->originalParallelTestingState['APP_SERVICES_CACHE'] instanceof UndefinedValue) {
            Env::set('APP_SERVICES_CACHE', $this->originalParallelTestingState['APP_SERVICES_CACHE']);
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
}
