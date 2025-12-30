<?php

namespace Orchestra\Testbench\Tests\WithWorkbenchTest;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\Contracts\Config as ConfigContract;

class MergeSeedersTestStub
{
    use WithWorkbench;

    public function __construct(protected bool $seed, protected string|false $seeders) {}

    public function __invoke(ConfigContract $config)
    {
        return $this->mergeSeedersForWorkbench($config);
    }

    public function shouldSeed(): bool
    {
        return $this->seed;
    }

    public function seeder(): string|false
    {
        return $this->seeders;
    }
}
