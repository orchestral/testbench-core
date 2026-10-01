<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Illuminate\Support\Facades\ParallelTesting;
use Orchestra\Testbench\Contracts\Attributes\AfterEach as AfterEachContract;
use Orchestra\Testbench\Contracts\Attributes\BeforeEach as BeforeEachContract;

use function Orchestra\Testbench\artisan;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class WithCachedViews implements AfterEachContract, BeforeEachContract
{
    /**
     * Handle the attribute.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function beforeEach($app): void
    {
        $callback = function () use ($app) {
            artisan($app, 'view:cache');

            if ($app->bound('view')) {
                $app->make('view')->flushFinderCache();
            }
        };

        if (isset($_SERVER['TEST_TOKEN'])) {
            ParallelTesting::setUpTestCase($callback);

            return;
        }

        value($callback);
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
    }
}
