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
        $from = str_replace(package_path().'/', '', (string) realpath($from));

        $to = str_replace(base_path().'/', '', (string) realpath($to));

        $this->components->task(\sprintf(
            'Copying %s [%s] to [%s]',
            $type,
            $from,
            $to,
        ));
    }
}
