<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

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
        if ($this->option('force') || ! $filesystem->exists("{$workingPath}/testbench.yaml")) {
            $filesystem->copy(__DIR__.'/stubs/testbench.yaml', "{$workingPath}/testbench.yaml");
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
        $environmentFile = $this->laravel->basePath('.env.example');

        if (! $filesystem->exists($environmentFile)) {
            return;
        }

        if ($this->option('force') || ! $filesystem->exists("{$workingPath}/{$this->environmentFile}")) {
            $filesystem->copy($environmentFile, "{$workingPath}/{$this->environmentFile}");
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
        $database = $this->laravel->databasePath('database.sqlite');
        $exampleDatabase = $this->laravel->databasePath('database.sqlite.example');

        if (! $filesystem->exists($exampleDatabase)) {
            return;
        }

        if ($this->option('force') || ! $filesystem->exists($database)) {
            $filesystem->copy($exampleDatabase, $database);
        }
    }
}
