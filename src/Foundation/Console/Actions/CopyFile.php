<?php

namespace Orchestra\Testbench\Foundation\Console\Actions;

use Illuminate\Console\View\Components\Factory as ComponentsFactory;
use Illuminate\Filesystem\Filesystem;

class CopyFile extends Action
{
    /**
     * Construct a new copy file instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  \Illuminate\Console\View\Components\Factory|null  $components
     * @param  bool  $force
     * @param  string|null  $workingPath
     */
    public function __construct(
        public Filesystem $filesystem,
        public ?ComponentsFactory $components = null,
        public bool $force = false,
        ?string $workingPath = null
    ) {
        $this->workingPath = $workingPath;
    }

    /**
     * Handle the action.
     *
     * @param  string  $from
     * @param  string  $to
     * @return void
     */
    public function handle(string $from, string $to): void
    {
        if (! $this->filesystem->exists($from)) {
            $this->components?->twoColumnDetail(
                sprintf('File [%s] already exists', $this->pathLocation($from)),
                '<fg=yellow;options=bold>SKIPPED</>'
            );
        }

        if ($this->force || ! $this->filesystem->exists($to)) {
            $this->filesystem->copy($from, $to);

            $this->components?->task(
                sprintf('Copying file [%s] to [%s]', $this->pathLocation($from), $this->pathLocation($to))
            );
        } else {
            $this->components?->twoColumnDetail(
                sprintf('File [%s] already exists', $this->pathLocation($to)),
                '<fg=yellow;options=bold>SKIPPED</>'
            );
        }
    }
}
