<?php

namespace Orchestra\Testbench\Foundation\Console\Actions;

use Illuminate\Console\View\Components\Factory as ComponentsFactory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\LazyCollection;

use function Laravel\Prompts\confirm;

class DeleteFiles extends Action
{
    /**
     * Construct a new action instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  \Illuminate\Console\View\Components\Factory  $components
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
     * @param  iterable<int, string>  $files
     * @return void
     */
    public function handle(iterable $files): void
    {
        LazyCollection::make($files)
            ->reject(static fn ($file) => str_ends_with($file, '.gitkeep') || str_ends_with($file, '.gitignore'))
            ->each(function ($file) {
                $location = $this->pathLocation($file);

                if (! $this->filesystem->exists($file)) {
                    $this->components?->twoColumnDetail(
                        sprintf('File [%s] doesn\'t exists', $location),
                        '<fg=yellow;options=bold>SKIPPED</>'
                    );

                    return;
                }

                if ($this->confirmation === true && confirm(sprintf('Delete [%s] file?', $location)) === false) {
                    return;
                }

                $this->filesystem->delete($file);

                $this->components?->task(
                    sprintf('File [%s] has been deleted', $location)
                );
            });
    }
}
