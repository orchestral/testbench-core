<?php

namespace Orchestra\Testbench\Foundation\Console\Actions;

use Illuminate\Console\View\Components\Factory as ComponentsFactory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\LazyCollection;

use function Laravel\Prompts\confirm;
use function Orchestra\Testbench\join_paths;

class EnsureDirectoryExists extends Action
{
    /**
     * Construct a new action instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  \Illuminate\Console\View\Components\Factory|null  $components
     * @param  string|null  $workingPath
     * @param  bool  $confirmation
     */
    public function __construct(
        public readonly Filesystem $filesystem,
        public readonly ?ComponentsFactory $components = null,
        ?string $workingPath = null,
        public readonly bool $confirmation = false
    ) {
        $this->workingPath = $workingPath;
    }

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
                $location = $this->pathLocation($directory);

                if ($this->filesystem->isDirectory($directory)) {
                    $this->components?->twoColumnDetail(
                        \sprintf('Directory [%s] already exists', $location),
                        '<fg=yellow;options=bold>SKIPPED</>'
                    );

                    return;
                }

                if ($this->confirmation === true && confirm(\sprintf('Ensure [%s] directory exists?', $location)) === false) {
                    return;
                }

                $this->filesystem->ensureDirectoryExists($directory, 0755, true);
                $this->filesystem->copy((string) realpath(join_paths(__DIR__, 'stubs', '.gitkeep')), join_paths($directory, '.gitkeep'));

                $this->components?->task(\sprintf('Prepare [%s] directory', $location));
            });
    }
}
