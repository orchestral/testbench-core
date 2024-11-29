<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\LazyCollection;
use Orchestra\Testbench\Foundation\Env;
use Symfony\Component\Console\Attribute\AsCommand;
use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\Foundation\Console\Concerns\CopyTestbenchFiles;
use Orchestra\Testbench\Workbench\Actions\AddAssetSymlinkFolders;

use function Orchestra\Testbench\package_path;

/**
 * @codeCoverageIgnore
 */
#[AsCommand(name: 'package:sync-skeleton', description: 'Sync skeleton folder to be served externally')]
class SyncSkeletonCommand extends Command
{
    use CopyTestbenchFiles;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:sync-skeleton';

    /**
     * The environment file name.
     *
     * @var string
     */
    protected $environmentFile = '.env';

    /** {@inheritDoc} */
    #[\Override]
    protected function configure()
    {
        $this->environmentFile = Env::get('TESTBENCH_ENVIRONMENT_FILE_USING') ?? $this->environmentFile;

        parent::configure();
    }

    /**
     * Execute the console command.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  \Orchestra\Testbench\Contracts\Config  $config
     * @return int
     */
    public function handle(Filesystem $filesystem, ConfigContract $config)
    {
        $this->copyTestbenchConfigurationFile($this->laravel, $filesystem, package_path());
        
        (new AddAssetSymlinkFolders($filesystem, $config))->handle();
    }
}
