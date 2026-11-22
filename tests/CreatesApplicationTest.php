<?php

namespace Orchestra\Testbench\Tests;

use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Support\Env;
use Orchestra\Testbench\Concerns\CreatesApplication;
use Orchestra\Testbench\Foundation\Application as Testbench;
use Orchestra\Testbench\PHPUnit\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CreatesApplicationTest extends TestCase
{
    use CreatesApplication;

    /** {@inheritDoc} */
    #[\Override]
    protected function tearDown(): void
    {
        Testbench::flushState($this);

        parent::tearDown();
    }

    #[Test]
    public function it_properly_loads_laravel_application()
    {
        $app = $this->createApplication();

        $this->assertInstanceOf(Application::class, $app);
        $this->assertTrue($app->bound('config'));
        $this->assertTrue($app->bound('view'));
    }

    #[Test]
    public function it_uses_a_services_manifest_per_parallel_testing_worker()
    {
        $this->withParallelTestingEnvironment('7', null, function () {
            $app = $this->createApplication();

            $this->assertSame(
                $this->normalizePath($app->bootstrapPath('cache/test_7_services.php')),
                $this->normalizePath($app->getCachedServicesPath())
            );
        });
    }

    #[Test]
    public function it_uses_a_services_manifest_per_laravel_parallel_testing_worker()
    {
        $this->withParallelTestingEnvironment('7', null, function () {
            $app = $this->createApplication();

            $this->assertSame(
                $this->normalizePath($app->bootstrapPath('cache/test_7_services.php')),
                $this->normalizePath($app->getCachedServicesPath())
            );
        }, laravelParallelTesting: true);
    }

    #[Test]
    public function it_keeps_the_default_services_manifest_when_not_testing_in_parallel()
    {
        $this->withParallelTestingEnvironment(null, null, function () {
            $app = $this->createApplication();

            $this->assertSame(
                $this->normalizePath($app->bootstrapPath('cache/services.php')),
                $this->normalizePath($app->getCachedServicesPath())
            );
        });
    }

    #[Test]
    public function it_keeps_an_explicit_services_manifest_when_testing_in_parallel()
    {
        $this->withParallelTestingEnvironment('7', 'bootstrap/cache/custom_services.php', function () {
            $app = $this->createApplication();

            $this->assertSame(
                $this->normalizePath($app->basePath('bootstrap/cache/custom_services.php')),
                $this->normalizePath($app->getCachedServicesPath())
            );
        });
    }

    /**
     * Compare paths regardless of the directory separator Laravel joined them with.
     */
    private function normalizePath(string $path): string
    {
        return str_replace('\\', '/', $path);
    }

    /**
     * Run the callback as a parallel testing worker (with a token), or as a serial run (without one).
     *
     * Pest and ParaTest set only the token for a package's workers; Laravel's own runner also sets
     * `LARAVEL_PARALLEL_TESTING`.
     *
     * @param  \Closure():void  $callback
     */
    private function withParallelTestingEnvironment(
        ?string $token,
        ?string $servicesCache,
        Closure $callback,
        bool $laravelParallelTesting = false
    ): void {
        $keys = ['LARAVEL_PARALLEL_TESTING', 'TEST_TOKEN', 'APP_SERVICES_CACHE'];
        $previous = [];

        foreach ($keys as $key) {
            $previous[$key] = $_SERVER[$key] ?? null;
            unset($_SERVER[$key], $_ENV[$key]);
            Env::getRepository()->clear($key);
        }

        if ($laravelParallelTesting) {
            $_SERVER['LARAVEL_PARALLEL_TESTING'] = '1';
        }

        if (! \is_null($token)) {
            $_SERVER['TEST_TOKEN'] = $token;
        }

        if (! \is_null($servicesCache)) {
            $_SERVER['APP_SERVICES_CACHE'] = $_ENV['APP_SERVICES_CACHE'] = $servicesCache;
        }

        try {
            $callback();
        } finally {
            foreach ($keys as $key) {
                unset($_SERVER[$key], $_ENV[$key]);
                Env::getRepository()->clear($key);

                if (! \is_null($previous[$key])) {
                    $_SERVER[$key] = $previous[$key];
                }
            }
        }
    }
}
