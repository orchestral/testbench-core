<?php

namespace Orchestra\Testbench\Foundation\Console\Actions;

use Illuminate\Console\View\Components\Factory as ComponentsFactory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\LazyCollection;

use function Orchestra\Testbench\transform_realpath_to_relative;

/**
 * @api
 */
class DeleteFiles extends Action
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
     * @param  iterable<int, string>  $files
     * @return void
     */
    public function handle(iterable $files): void
    {
        LazyCollection::make($files)
            ->reject(static fn ($file) => str_ends_with($file, '.gitkeep') || str_ends_with($file, '.gitignore'))
            ->each(function ($file) {
                if (! $this->filesystem->exists($file)) {
                    $this->components?->twoColumnDetail(
                        \sprintf('File [%s] doesn\'t exists', transform_realpath_to_relative($file, $this->workingPath)),
                        '<fg=yellow;options=bold>SKIPPED</>'
                    );

                    return;
                }

                $this->filesystem->delete($file);

                $this->components?->task(
                    \sprintf('File [%s] has been deleted', transform_realpath_to_relative($file, $this->workingPath))
                );
            });
    }
}
