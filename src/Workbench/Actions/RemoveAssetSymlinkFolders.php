<?php

namespace Orchestra\Testbench\Workbench\Actions;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Orchestra\Testbench\Contracts\Config as ConfigContract;

use function Orchestra\Testbench\package_path;

/**
 * @internal
 */
final class RemoveAssetSymlinkFolders
{
    /**
     * Construct a new action.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  \Orchestra\Testbench\Contracts\Config  $config
     */
    public function __construct(
        protected Filesystem $files,
        protected ConfigContract $config
    ) {}

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle(): void
    {
        /** @var array<int, array{from: string, to: string}> $sync */
        $sync = $this->config->getWorkbenchAttributes()['sync'] ?? [];

        Collection::make($sync)
            ->map(static function ($pair) {
                /** @var string $from */
                $from = package_path($pair['from']);

                /** @var string $to */
                $to = base_path($pair['to']);

                if (windows_os() && is_dir($to) && readlink($to) !== $to) {
                    return [$to, static function ($to) {
                        @rmdir($to);
                    }];
                } elseif (is_link($to)) {
                    return [$to, static function ($to) {
                        @unlink($to);
                    }];
                }

                return null;
            })->filter()
            ->each(static function ($payload) {
                /** @var array{0: string, 1: (\Closure(string):(void))} $payload */
                value($payload[1], $payload[0]);

                @clearstatcache(false, \dirname($payload[0]));
            });
    }
}
