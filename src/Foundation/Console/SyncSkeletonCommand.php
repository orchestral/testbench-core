<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

/**
 * @codeCoverageIgnore
 */
#[AsCommand(name: 'package:sync-skeleton', description: 'Sync skeleton folder to be served externally')]
class SyncSkeletonCommand extends Command
{
    //
}
