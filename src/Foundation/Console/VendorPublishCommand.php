<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Foundation\Console\VendorPublishCommand as Command;

use function Orchestra\Testbench\package_path;

/**
 * @codeCoverageIgnore
 */
class VendorPublishCommand extends Command
{
    /** {@inheritDoc} */
    #[\Override]
    protected function status($from, $to, $type)
    {
        $laravelPath = base_path();
        $packagePath = package_path();

        $pathLocation = function ($path) use ($laravelPath, $packagePath) {
            $path = (string) realpath($path);

            return match (true) {
                str_starts_with($path, $laravelPath) => str_replace("{$laravelPath}/", '@laravel/', $path),
                str_starts_with($path, $packagePath) => str_replace("{$packagePath}/", './', $path),
                default => $path,
            };
        };

        $fromLocation = $pathLocation($from);
        $toLocation = $pathLocation($to);

        if (
            $type === 'directory' &&
            $fromLocation === $toLocation &&
            is_link($to)
        ) {
            $this->components->task('Synced directory');

            return;
        }

        $this->components->task(\sprintf(
            'Copying %s [%s] to [%s]',
            $type,
            $fromLocation,
            $toLocation,
        ));
    }
}
