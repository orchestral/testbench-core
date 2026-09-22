<?php

namespace Orchestra\Testbench\Foundation\Console\Actions;

use Illuminate\Console\View\Components\Factory as ComponentsFactory;
use Illuminate\Filesystem\Filesystem;

use function Orchestra\Testbench\transform_realpath_to_relative;

/**
 * @api
 */
class GeneratesFile extends Action
{
    /**
     * Construct a new action instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  \Illuminate\Console\View\Components\Factory|null  $components
     * @param  bool  $force
     * @param  string|null  $workingPath
     * @param  bool  $pretending
     */
    public function __construct(
        public Filesystem $filesystem,
        public ?ComponentsFactory $components = null,
        public bool $force = false,
        public ?string $workingPath = null,
        bool $pretending = false,
    ) {
        $this->pretending = $pretending;
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

        Task::action(function () use ($from, $to) {
            $copied = $this->filesystem->copy($from, $to);

            $gitKeepFile = \sprintf('%s/.gitkeep', \dirname($to));

            if ($this->filesystem->exists($gitKeepFile)) {
                $this->filesystem->delete($gitKeepFile);
            }

            return $copied;
        })->response(function () use ($to) {
            $this->components?->task(
                \sprintf('File [%s] generated', transform_realpath_to_relative($to, $this->workingPath))
            );
        })->requirements(function () use ($from, $to) {
            if (! $this->filesystem->exists($from)) {
                $this->components?->twoColumnDetail(
                    \sprintf('Source file [%s] doesn\'t exists', transform_realpath_to_relative($from, $this->workingPath)),
                    '<fg=yellow;options=bold>SKIPPED</>'
                );

                return false;
            }

            if (! $this->force && $this->filesystem->exists($to)) {
                $this->components?->twoColumnDetail(
                    \sprintf('File [%s] already exists', transform_realpath_to_relative($to, $this->workingPath)),
                    '<fg=yellow;options=bold>SKIPPED</>'
                );

                return false;
            }

            return true;
        })->dispatch($this->pretending);
    }
}
