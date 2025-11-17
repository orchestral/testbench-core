<?php

namespace Orchestra\Testbench\Tests\Workbench;

use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\Tests\TestCase;
use Orchestra\Testbench\Workbench\Actions\AddAssetSymlinkFolders;
use Orchestra\Testbench\Workbench\Actions\RemoveAssetSymlinkFolders;
use PHPUnit\Framework\Attributes\Test;

use function Orchestra\Sidekick\is_symlink;
use function Orchestra\Sidekick\join_paths;
use function Orchestra\Testbench\default_skeleton_path;
use function Orchestra\Testbench\workbench_path;

class ActionsTest extends TestCase
{
    protected Filesystem $filesystem;

    #[\Override]
    protected function setUp(): void
    {
        $this->filesystem = new Filesystem;
        $skeletonPath = default_skeleton_path();

        $this->afterApplicationCreated(function () use ($skeletonPath) {
            $this->filesystem->copyDirectory(join_paths($skeletonPath, 'storage'), join_paths($skeletonPath, 'storage.bak'));
            $this->ensureSymlinkExists();
        });

        $this->beforeApplicationDestroyed(function () use ($skeletonPath) {
            if (! default_skeleton_path('storage', 'framework')) {
                $this->filesystem->moveDirectory(join_paths($skeletonPath, 'storage.bak'), join_paths($skeletonPath, 'storage'), true);
            } else {
                $this->filesystem->deleteDirectory(join_paths($skeletonPath, 'storage.bak'));
            }
        });
        
        parent::setUp();

    }

    #[Test]
    public function it_does_not_wipe_target_directory_while_recreating_asset_symlink()
    {
        (new AddAssetSymlinkFolders($this->filesystem, $this->getConfig()))->handle();

        $this->assertDirectoryExists(default_skeleton_path('storage', 'framework'));
    }

    #[Test]
    public function it_does_not_wipe_target_directory_while_removing_asset_symlink()
    {
        (new RemoveAssetSymlinkFolders($this->filesystem, $this->getConfig()))->handle();

        $this->assertDirectoryExists(default_skeleton_path('storage', 'framework'));
    }

    protected function ensureSymlinkExists(): void
    {
        if (! is_symlink(workbench_path('storage'))) {
            $this->filesystem->link(default_skeleton_path('storage'), workbench_path('storage'));
        }
    }

    protected function getConfig(): Config
    {
        return new Config([
            'workbench' => [
                'sync' => [[
                    'from' => 'storage',
                    'to' => 'workbench/storage',
                    'reverse' => true,
                ]],
            ],
        ]);
    }
}
