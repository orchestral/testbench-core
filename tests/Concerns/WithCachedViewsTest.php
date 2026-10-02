<?php

namespace Orchestra\Testbench\Tests\Concerns;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Orchestra\Testbench\Concerns\WithCachedViews;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;

use function Orchestra\Testbench\workbench_path;

class WithCachedViewsTest extends TestCase
{
    use WithCachedViews;
    use WithWorkbench;

    /** {@inheritDoc} */
    protected function defineEnvironment($app): void
    {
        tap($app->make('config'), function (ConfigRepository $config) {
            $config->set('view.paths', [workbench_path('resources', 'views')]);
        });
    }

    /**
     * @test
     */
    public function it_caches_and_clears_views()
    {
        $compiledPath = $this->getCompiledPathForView('testbench');

        $this->assertFileExists($compiledPath);

        $this->tearDownWithCachedViews();

        $this->assertFileDoesNotExist($compiledPath);
    }

    /**
     * Get the compiled path for a view name.
     */
    protected function getCompiledPathForView(string $name): string
    {
        $path = view($name)->getPath();

        return $this->app->make('blade.compiler')->getCompiledPath(realpath($path) ?: $path);
    }
}
