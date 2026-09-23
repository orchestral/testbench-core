<?php

namespace Orchestra\Testbench\Foundation\Console\Actions;

use Illuminate\Console\View\Components\Factory as ComponentsFactory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\LazyCollection;
use Orchestra\Sidekick\Console\Task;

use function Laravel\Prompts\confirm;
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
     * @param  bool  $confirmation
     * @param  bool  $pretending
     */
    public function __construct(
        public Filesystem $filesystem,
        public ?ComponentsFactory $components = null,
        public ?string $workingPath = null,
        public bool $confirmation = false,
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
                $location = transform_realpath_to_relative($directory, $this->workingPath);

                Task::action(fn () => $this->filesystem->deleteDirectory($directory))
                    ->response(function () use ($location) {
                        $this->components?->task(
                            \sprintf('Directory [%s] has been deleted', $location)
                        );
                    })->requirements(function () use ($directory, $location) {
                        if (! $this->filesystem->isDirectory($directory)) {
                            $this->components?->twoColumnDetail(
                                \sprintf('Directory [%s] doesn\'t exists', $location),
                                '<fg=yellow;options=bold>SKIPPED</>'
                            );

                            return false;
                        }

                        if ($this->confirmation === true && confirm(\sprintf('Delete [%s] directory?', $location)) === false) {
                            return false;
                        }

                        return true;
                    })->dispatch($this->pretending);
            });
    }
}
