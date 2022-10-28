<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Filesystem\Filesystem;

class ServeCommand extends \Illuminate\Foundation\Console\ServeCommand
{
    /**
     * Execute the console command.
     *
     * @return int
     *
     * @throws \Exception
     */
    public function handle()
    {
        $filesystem = new Filesystem();

        /** @phpstan-ignore-next-line */
        $configurationFile = TESTBENCH_WORKING_PATH.'/testbench.yaml';
        $laravelBasePath = $this->laravel->basePath();

        if ($filesystem->exists($configurationFile)) {
            $filesystem->copy($configurationFile, "{$laravelBasePath}/testbench.yaml");
        }

        parent::handle();
    }
}
