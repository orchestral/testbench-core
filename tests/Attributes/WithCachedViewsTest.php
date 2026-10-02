<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Orchestra\Testbench\Attributes\WithCachedViews;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;

use function Orchestra\Testbench\workbench_path;

class WithCachedViewsTest extends TestCase
{
    use WithWorkbench;

    /** {@inheritDoc} */
    protected function defineEnvironment($app): void
    {
        tap($app->make('config'), function (ConfigRepository $config) {
            $config->set('view.paths', [workbench_path('resources', 'views')]);
        });
    }

    /** @test */
    #[WithCachedViews]
    public function it_can_cached_views()
    {
        $compiledPath = realpath($this->getCompiledPathForView('testbench'));

        $this->assertNotFalse($compiledPath);
        $this->assertFileExists($compiledPath);
    }

    /**
     * @test
     * @depends it_can_cached_views
     */
    public function it_does_not_persist_cache_after_test()
    {
        $compiledPath = realpath($this->getCompiledPathForView('testbench'));

        $this->assertFalse($compiledPath);
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