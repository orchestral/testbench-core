<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Orchestra\Testbench\Attributes\WithCachedViews;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

use function Orchestra\Sidekick\Filesystem\join_paths;
use function Orchestra\Testbench\workbench_path;

#[Group('without-parallel')]
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

    #[Test]
    #[WithCachedViews]
    public function it_can_cached_views()
    {
        $compiledPath = $this->getCompiledPathForView('testbench');

        $this->assertFileExists($compiledPath);
    }

    #[Test]
    #[Depends('it_can_cached_views')]
    public function it_does_not_persist_cache_after_test()
    {
        $compiledPath = $this->getCompiledPathForView('testbench');

        $this->assertFileDoesNotExist($compiledPath);
    }

    /**
     * Get the compiled path for a view name.
     */
    protected function getCompiledPathForView(string $name): string
    {
        return storage_path(
            join_paths(...[
                'framework',
                'views',
                hash('xxh128', 'v2'.view($name)->getPath()).'.php',
            ])
        );
    }
}
