<?php

namespace Orchestra\Testbench\Foundation\Console\Actions;

use Illuminate\Console\View\Components\Factory as ComponentsFactory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\LazyCollection;
use Orchestra\Sidekick\Console\Task;

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
     * @param  iterable<int, string>  $files
     * @return void
     */
    public function handle(iterable $files): void
    {
        (new LazyCollection($files))
            ->reject(static fn ($file) => str_ends_with($file, '.gitkeep') || str_ends_with($file, '.gitignore'))
            ->each(function ($file) {
                $location = transform_realpath_to_relative($file, $this->workingPath);

                Task::action(fn () => $this->filesystem->delete($file))
                    ->response(function () use ($location) {
                        $this->components?->task(
                            \sprintf('File [%s] has been deleted', $location)
                        );
                    })->requirements(function () use ($file, $location) {
                        if (! $this->filesystem->exists($file)) {
                            $this->components?->twoColumnDetail(
                                \sprintf('File [%s] doesn\'t exists', $location),
                                '<fg=yellow;options=bold>SKIPPED</>'
                            );

                            return false;
                        }

                        return true;
                    })->dispatch($this->pretending);
            });
    }
}
