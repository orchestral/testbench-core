<?php

namespace Orchestra\Testbench\Foundation\Console\Actions;

use Illuminate\Console\View\Components\Factory as ComponentsFactory;
use Illuminate\Filesystem\Filesystem;

class GeneratesFile extends Action
{
    /**
     * Construct a new action instance.
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
     * @param  string|false|null  $from
     * @param  string|false|null  $to
     * @return void
     */
    public function handle($from, $to): void
    {
        if (! \is_string($from) || ! \is_string($to)) {
            return;
        }

        if (! $this->filesystem->exists($from)) {
            $this->components?->twoColumnDetail(
                \sprintf('Source file [%s] doesn\'t exists', $this->pathLocation($from)),
                '<fg=yellow;options=bold>SKIPPED</>'
            );

            return;
        }

        if (! $this->force && $this->filesystem->exists($to)) {
            $this->components?->twoColumnDetail(
                \sprintf('File [%s] already exists', $this->pathLocation($to)),
                '<fg=yellow;options=bold>SKIPPED</>'
            );

            return;
        }

        $this->filesystem->copy($from, $to);

        $gitKeepFile = \sprintf('%s/.gitkeep', \dirname($to));

        if ($this->filesystem->exists($gitKeepFile)) {
            $this->filesystem->delete($gitKeepFile);
        }

        $this->components?->task(
            \sprintf('File [%s] generated', $this->pathLocation($to))
        );
    }
}
