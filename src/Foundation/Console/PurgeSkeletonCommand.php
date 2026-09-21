<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Orchestra\Sidekick\Env;
use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\Foundation\Actions\DeleteVendorSymlink;
use Orchestra\Testbench\Foundation\Console\Actions\Task;
use Orchestra\Testbench\Workbench\Actions\RemoveAssetSymlinkFolders;
use Symfony\Component\Console\Attribute\AsCommand;

use function Orchestra\Sidekick\Filesystem\join_paths;

/**
 * @codeCoverageIgnore
 */
#[AsCommand(name: 'package:purge-skeleton', description: 'Purge skeleton folder to original state')]
class PurgeSkeletonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:purge-skeleton
                                {--pretend : Outputs the operations but will not execute anything}';

    /**
     * Execute the console command.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  \Orchestra\Testbench\Contracts\Config  $config
     * @return int
     */
    public function handle(Filesystem $filesystem, ConfigContract $config)
    {
        /** @var bool $pretending */
        $pretending = $this->option('pretend');

        $runCommand = function ($name) use ($pretending) {
            (new Task(
                action: function () use ($name) {
                    $this->call($name);

                    return true;
                },
                response: function ($action, $pretending) use ($name) {
                    if ($pretending) {
                        $this->components?->task(
                            \sprintf('Command [%s] executed', $name)
                        );
                    }
                }
            ))($pretending);
        };

        $runCommand('config:clear');
        $runCommand('event:clear');
        $runCommand('route:clear');
        $runCommand('view:clear');

        (new RemoveAssetSymlinkFolders($filesystem, $config))->handle();

        ['files' => $files, 'directories' => $directories] = $config->getPurgeAttributes();

        $environmentFile = Env::get('TESTBENCH_ENVIRONMENT_FILENAME', '.env');

        (new Actions\DeleteFiles(
            filesystem: $filesystem,
            pretending: $pretending,
        ))->handle(
            (new Collection([
                $environmentFile,
                "{$environmentFile}.backup",
                join_paths('bootstrap', 'cache', 'testbench.yaml'),
                join_paths('bootstrap', 'cache', 'testbench.yaml.backup'),
            ]))->map(fn ($file) => $this->laravel->basePath($file))
        );

        (new Actions\DeleteFiles(
            filesystem: $filesystem,
            pretending: $pretending,
        ))->handle(
            (new LazyCollection(function () use ($filesystem) {
                yield $this->laravel->basePath(join_paths('database', 'database.sqlite'));
                yield $filesystem->glob($this->laravel->basePath(join_paths('routes', 'testbench-*.php')));
                yield $filesystem->glob($this->laravel->basePath(join_paths('storage', 'app', 'public', '*')));
                yield $filesystem->glob($this->laravel->basePath(join_paths('storage', 'app', '*')));
                yield $filesystem->glob($this->laravel->basePath(join_paths('storage', 'framework', 'sessions', '*')));
            }))->flatten()
        );

        (new Actions\DeleteFiles(
            filesystem: $filesystem,
            components: $this->components,
            pretending: $pretending,
        ))->handle(
            (new LazyCollection($files))
                ->map(fn ($file) => $this->laravel->basePath($file))
                ->map(static function ($file) use ($filesystem) {
                    return str_contains($file, '*')
                        ? [...$filesystem->glob($file)]
                        : $file;
                })->flatten()
                ->reject(fn ($file) => str_contains($file, '*'))
        );

        (new Actions\DeleteDirectories(
            filesystem: $filesystem,
            components: $this->components,
            pretending: $pretending,
        ))->handle(
            (new Collection($directories))
                ->map(fn ($directory) => $this->laravel->basePath($directory))
                ->map(static function ($directory) use ($filesystem) {
                    return str_contains($directory, '*')
                        ? [...$filesystem->glob($directory)]
                        : $directory;
                })->flatten()
                ->reject(static function ($directory) {
                    return str_contains($directory, '*');
                })
        );

        TerminatingConsole::before(function () {
            (new DeleteVendorSymlink)->handle($this->laravel);
        });

        return Command::SUCCESS;
    }
}
