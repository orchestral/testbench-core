<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Orchestra\Testbench\Contracts\Attributes\AfterEach as AfterEachContract;
use Orchestra\Testbench\Contracts\Attributes\BeforeEach as BeforeEachContract;

use function Orchestra\Sidekick\Filesystem\join_paths;
use function Orchestra\Testbench\artisan;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class WithCachedViews implements AfterEachContract, BeforeEachContract
{
    /**
     * The original compiled views path.
     */
    protected ?string $originalCompiledPath = null;

    /**
     * Handle the attribute.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function beforeEach($app): void
    {
        /** @var \Illuminate\Contracts\Config\Repository $config */
        $config = $app->make('config');
        $this->originalCompiledPath = $config->get('view.compiled');

        $config->set('view.compiled', join_paths(
            $app->storagePath(join_paths('framework', 'views')),
            'testbench-'.bin2hex(random_bytes(16))
        ));

        artisan($app, 'view:cache');

        clearstatcache(false, $config->get('view.compiled'));

        if ($app->bound('view')) {
            $app->make('view')->flushFinderCache();
        }
    }

    /**
     * Handle the attribute.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function afterEach($app): void
    {
        artisan($app, 'view:clear');

        /** @var \Illuminate\Contracts\Config\Repository $config */
        $config = $app->make('config');
        $config->set('view.compiled', $this->originalCompiledPath);
    }
}
