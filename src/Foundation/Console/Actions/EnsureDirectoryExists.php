<?php

namespace Orchestra\Testbench\Foundation\Console\Actions;

use Illuminate\Console\View\Components\Factory as ComponentsFactory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\LazyCollection;

use function Orchestra\Sidekick\join_paths;
use function Orchestra\Testbench\transform_realpath_to_relative;

/**
 * @api
 */
class EnsureDirectoryExists extends Action
{
    /**
     * Construct a new action instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  \Illuminate\Console\View\Components\Factory|null  $components
     * @param  string|null  $workingPath
     */
    public function __construct(
        public Filesystem $filesystem,
        public ?ComponentsFactory $components = null,
        public ?string $workingPath = null
    ) {}

    /**
     * Handle the action.
     *
     * @param  iterable<int, string>  $directories
     * @return void
     */
    public function handle(iterable $directories): void
    {
        LazyCollection::make($directories)
            ->each(function ($directory) {
                if ($this->filesystem->isDirectory($directory)) {
                    $this->components?->twoColumnDetail(
                        \sprintf('Directory [%s] already exists', transform_realpath_to_relative($directory, $this->workingPath)),
                        '<fg=yellow;options=bold>SKIPPED</>'
                    );

                    return;
                }

                $this->filesystem->ensureDirectoryExists($directory, 0755, true);
                $this->filesystem->copy((string) realpath(join_paths(__DIR__, 'stubs', '.gitkeep')), join_paths($directory, '.gitkeep'));

                $this->components?->task(\sprintf('Prepare [%s] directory', transform_realpath_to_relative($directory, $this->workingPath)));
            });
    }
}
