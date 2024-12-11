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

            if (str_starts_with($path, $laravelPath)) {
                return str_replace("{$laravelPath}/", '@laravel/', $path);
            } elseif (str_starts_with($path, $packagePath)) {
                return str_replace("{$packagePath}/", './', $path);
            }

            return $path;
        };

        $fromLocation = $pathLocation($from);
        $toLocation = $pathLocation($to);

        if (
            $fromLocation === $toLocation &&
            is_link($to) &&
            $type === 'directory'
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
