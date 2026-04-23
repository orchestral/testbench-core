<?php

namespace Orchestra\Testbench\Tests\Attributes\WithConfigTest;

use Illuminate\Support\ServiceProvider;

use function Orchestra\Sidekick\Filesystem\join_paths;

class WorkbenchServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->mergeConfigFrom(join_paths(__DIR__, 'config', 'testbench.php'), 'testbench');
    }
}
