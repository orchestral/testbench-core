<?php

namespace Orchestra\Testbench\Workbench\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'workbench:install')]
class InstallCommand extends Command
{
    use Concerns\InteractsWithIO;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workbench:install {--force : Overwrite any existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup Workbench for package development';

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

        $this->prepareWorkbenchDirectories($filesystem, $workingPath);

        $this->copyTestbenchConfigurationFile($filesystem, $workingPath);
        $this->copyTestbenchDotEnvFile($filesystem, $workingPath);

        $this->call('package:create-sqlite-db', ['--force' => true]);

        return Command::SUCCESS;
    }

    /**
     * Prepare workbench directories.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  string  $workingPath
     * @return void
     */
    protected function prepareWorkbenchDirectories(Filesystem $filesystem, string $workingPath): void
    {
        $workbenchWorkingPath = "{$workingPath}/workbench";

        $this->ensureDirectoryExists($filesystem, "{$workbenchWorkingPath}/app");
        $this->ensureDirectoryExists($filesystem, "{$workbenchWorkingPath}/database/factories");
        $this->ensureDirectoryExists($filesystem, "{$workbenchWorkingPath}/database/migrations");
        $this->ensureDirectoryExists($filesystem, "{$workbenchWorkingPath}/database/seeders");
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
        $from = (string) realpath(__DIR__.'/stubs/testbench.yaml');
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

        $to = "{$workingPath}/workbench/{$choice}";

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
     * Ensure a directory exists and add `.gitkeep` file.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  string  $workingPath
     * @return void
     */
    protected function ensureDirectoryExists(Filesystem $filesystem, string $workingPath): void
    {
        $filesystem->ensureDirectoryExists($workingPath, 0755, true);

        $filesystem->copy((string) realpath(__DIR__.'/stubs/.gitkeep'), "{$workingPath}/.gitkeep");
    }
}
