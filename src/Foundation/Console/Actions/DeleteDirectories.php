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
     * @param  bool  $pretending
     */
    public function __construct(
        public Filesystem $filesystem,
        public ?ComponentsFactory $components = null,
        public ?string $workingPath = null,
        bool $pretending = false,
    ) {
        $this->pretending = $pretending;
    }

    /**
     * Handle the action.
     *
     * @param  iterable<int, string>  $directories
     * @return void
     */
    public function handle(iterable $directories): void
    {
        (new LazyCollection($directories))
            ->each(function ($directory) {
                $task = new Task(
                    requirement: function () use ($directory) {
                        if (! $this->filesystem->isDirectory($directory)) {
                            $this->components?->twoColumnDetail(
                                \sprintf('Directory [%s] doesn\'t exists', transform_realpath_to_relative($directory, $this->workingPath)),
                                '<fg=yellow;options=bold>SKIPPED</>'
                            );

                            return false;
                        }

                        return true;
                    },
                    action: fn () => $this->filesystem->deleteDirectory($directory),
                    response: function () use ($directory) {
                        $this->components?->task(
                            \sprintf('Directory [%s] has been deleted', transform_realpath_to_relative($directory, $this->workingPath))
                        );
                    },
                );

                $task($this->pretending);
            });
    }
}
