<?php

namespace Orchestra\Testbench\Support;

use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\PhpExecutableFinder as SymfonyPhpExecutableFinder;

use function Orchestra\Testbench\join_paths;

class PhpExecutableFinder extends SymfonyPhpExecutableFinder
{
    /**
     * Finds The PHP executable.
     */
    public function find(bool $includeArgs = true): string|false
    {
        if ($herdPath = getenv('HERD_HOME')) {
            return (new ExecutableFinder)->find('php', false, [join_paths($herdPath, 'bin')]);
        }

        return parent::find($includeArgs);
    }
}