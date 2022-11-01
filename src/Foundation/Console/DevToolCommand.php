<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class DevToolCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:devtool {--force : Overwrite any existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup DevTool for package development';

    /**
     * The environment file name.
     *
     * @var string
     */
    protected $environmentFile = '.env';

    /**
     * Execute the console command.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @return int
     */
    public function handle(Filesystem $filesystem)
    {
        /** @phpstan-ignore-next-line */
        $workingPath = TESTBENCH_WORKING_PATH;

        $this->copyTestbenchConfigurationFile($filesystem, $workingPath);
        $this->copyTestbenchDotEnvFile($filesystem, $workingPath);
        $this->copySqliteDatabaseFile($filesystem);

        return Command::SUCCESS;
    }

    /**
     * Copy the "testbench.yaml" file.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  string  $workingPath
     * @return void
     */
    protected function copyTestbenchConfigurationFile(Filesystem $filesystem, string $workingPath): void
    {
        $from = realpath(__DIR__.'/stubs/testbench.yaml');
        $to = "{$workingPath}/testbench.yaml";

        if ($this->option('force') || ! $filesystem->exists($to)) {
            $filesystem->copy($from, $to);

            $this->status($from, $to, 'file');
        } else {
            $this->components->twoColumnDetail(
                sprintf('File [%s] already exists', str_replace($workingPath.'/', '', $to)),
                '<fg=yellow;options=bold>SKIPPED</>'
            );
        }
    }

    /**
     * Copy the ".env" file.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  string  $workingPath
     * @return void
     */
    protected function copyTestbenchDotEnvFile(Filesystem $filesystem, string $workingPath): void
    {
        $from = $this->laravel->basePath('.env.example');

        if (! $filesystem->exists($from)) {
            return;
        }

        $choices = Collection::make([
            $this->environmentFile,
            "{$this->environmentFile}.example",
            "{$this->environmentFile}.dist",
        ])->filter(fn ($file) => ! $filesystem->exists("{$workingPath}/{$file}"))
        ->values()
        ->prepend('Skip exporting .env')
        ->all();

        if (! $this->option('force') && empty($choices)) {
            $this->components->twoColumnDetail(
                'File [.env] already exists', '<fg=yellow;options=bold>SKIPPED</>'
            );

            return;
        }

        $choice = $this->components->choice(
            "Export '.env' file as?",
            $choices,
        );

        if ($choice === 'Skip exporting .env') {
            return;
        }

        $to = "{$workingPath}/{$choice}";

        if ($this->option('force') || ! $filesystem->exists($to)) {
            $filesystem->copy($from, $to);

            $this->status($from, $to, 'file');
        } else {
            $this->components->twoColumnDetail(
                sprintf('File [%s] already exists', str_replace($workingPath.'/', '', $to)),
                '<fg=yellow;options=bold>SKIPPED</>'
            );
        }
    }

    /**
     * Copy the "database.sqlite" file.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @return void
     */
    protected function copySqliteDatabaseFile(Filesystem $filesystem): void
    {
        $from = $this->laravel->databasePath('database.sqlite.example');
        $to = $this->laravel->databasePath('database.sqlite');

        if (! $filesystem->exists($from)) {
            return;
        }

        if ($this->option('force') || ! $filesystem->exists($to)) {
            $filesystem->copy($from, $to);

            $this->status($from, $to, 'file');
        } else {
            $this->components->twoColumnDetail(
                sprintf('File [%s] already exists', $to),
                '<fg=yellow;options=bold>SKIPPED</>'
            );
        }
    }


    /**
     * Write a status message to the console.
     *
     * @param  string  $from
     * @param  string  $to
     * @param  string  $type
     * @return void
     */
    protected function status(string $from, string $to, string $type): void
    {
        /** @phpstan-ignore-next-line */
        $workingPath = TESTBENCH_WORKING_PATH;

        $from = str_replace($workingPath.'/', '', realpath($from));

        $to = str_replace($workingPath.'/', '', realpath($to));

        $this->components->task(sprintf(
            'Copying %s [%s] to [%s]',
            $type,
            $from,
            $to,
        ));
    }
}
