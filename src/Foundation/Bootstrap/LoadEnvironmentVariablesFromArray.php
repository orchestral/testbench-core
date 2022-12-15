<?php

namespace Orchestra\Testbench\Foundation\Bootstrap;

use Dotenv\Parser\Parser;
use Dotenv\Store\StringStore;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Env;

final class LoadEnvironmentVariablesFromArray
{
    /**
     * The environment variables.
     *
     * @var array<int, mixed>
     */
    public $environmentVariables;

    /**
     * Construct a new Create Vendor Symlink bootstrapper.
     *
     * @param  array<int, mixed>  $environmentVariables
     */
    public function __construct(array $environmentVariables)
    {
        $this->environmentVariables = $environmentVariables;
    }

    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        $store = new StringStore(implode("\n", $this->environmentVariables));
        $parser = new Parser();

        collect($parser->parse($store->read()))
            ->filter(function ($entry) {
                /** @var \Dotenv\Parser\Entry $entry */
                return $entry->getValue()->isDefined();
            })->each(function ($entry) {
                /** @var \Dotenv\Parser\Entry $entry */
                Env::getRepository()->set($entry->getName(), $entry->getValue()->get()->getChars());
            });
    }
}
