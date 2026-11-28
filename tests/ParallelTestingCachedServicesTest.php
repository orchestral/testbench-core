<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\Foundation\Env;
use Orchestra\Testbench\TestCase;

use function Orchestra\Sidekick\Filesystem\join_paths;

class ParallelTestingCachedServicesTest extends TestCase
{
    /** {@inheritDoc} */
    #[\Override]
    protected function setUp(): void
    {
        $_SERVER['LARAVEL_PARALLEL_TESTING'] = 1;
        $_SERVER['TEST_TOKEN'] = '7';

        parent::setUp();
    }

    /** {@inheritDoc} */
    #[\Override]
    protected function tearDown(): void
    {
        $cachePath = $this->app->bootstrapPath('cache');

        parent::tearDown();

        foreach (['test_7_services.php', 'test_7_packages.php'] as $file) {
            if (is_file(join_paths($cachePath, $file))) {
                unlink(join_paths($cachePath, $file));
            }
        }

        unset($_SERVER['LARAVEL_PARALLEL_TESTING'], $_SERVER['TEST_TOKEN']);

        Env::forget('APP_SERVICES_CACHE');
    }

    /** @test */
    public function it_uses_a_cached_services_manifest_for_each_parallel_worker()
    {
        $this->assertSame(
            $this->app->basePath(join_paths('bootstrap', 'cache', 'test_7_services.php')),
            $this->app->getCachedServicesPath()
        );

        $this->assertFileExists($this->app->getCachedServicesPath());
    }
}
