<?php

namespace Orchestra\Testbench\Support;

use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\PhpExecutableFinder as SymfonyPhpExecutableFinder;

use function Orchestra\Testbench\join_paths;

class PhpExecutableFinder extends SymfonyPhpExecutableFinder
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    #[\Override]
    public function find(bool $includeArgs = true): string|false
    {
        if ($herdPath = getenv('HERD_HOME')) {
            return (new ExecutableFinder)->find(name: 'php', extraDirs: [join_paths($herdPath, 'bin')]) ?? false;
        }

        return parent::find($includeArgs);
    }
}
