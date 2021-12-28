<?php

namespace Orchestra\Testbench;

use Illuminate\Testing\PendingCommand;

/**
 * Create Laravel application instance.
 *
 * @param  string|null  $basePath
 * @return object
 */
function container(?string $basePath = null)
{
    return new class($basePath) {
        use Concerns\CreatesApplication;

        /**
         * The default base path.
         *
         * @var string|null
         */
        protected $basePath;

        /**
         * Construc a new container.
         *
         * @param  string|null  $basePath
         */
        public function __construct(?string $basePath)
        {
            $this->basePath = $basePath;
        }

        /**
         * Get base path.
         *
         * @return string
         */
        protected function getBasePath()
        {
            return $this->basePath ?? static::applicationBasePath();
        }
    };
}

/**
 * Run artisan command.
 *
 * @param  \Orchestra\Testbench\Contracts\TestCase  $testbench
 * @param  string  $command
 * @param  array<string, mixed>  $parameters
 *
 * @return \Illuminate\Testing\PendingCommand|int
 */
function artisan(Contracts\TestCase $testbench, string $command, array $parameters = [])
{
    return tap($testbench->artisan($command, $parameters), function ($artisan) {
        if ($artisan instanceof PendingCommand) {
            $artisan->run();
        }
    });
}
