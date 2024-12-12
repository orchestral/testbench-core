<?php

namespace Orchestra\Testbench\Foundation\Console\Actions;

use Illuminate\Console\View\Components\Factory as ComponentsFactory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\LazyCollection;

use function Orchestra\Testbench\transform_realpath_to_relative;

/**
 * @api
 */
class DeleteDirectories extends Action
{
    /**
     * Construct a new action instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  \Illuminate\Console\View\Components\Factory  $components
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
                if (! $this->filesystem->isDirectory($directory)) {
                    $this->components?->twoColumnDetail(
                        \sprintf('Directory [%s] doesn\'t exists', transform_realpath_to_relative($directory, $this->workingPath)),
                        '<fg=yellow;options=bold>SKIPPED</>'
                    );

                    return;
                }

                $this->filesystem->deleteDirectory($directory);

                $this->components?->task(
                    \sprintf('Directory [%s] has been deleted', transform_realpath_to_relative($directory, $this->workingPath))
                );
            });
    }
}
