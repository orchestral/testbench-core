<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;

class CommandTest extends TestCase
{
    use WithWorkbench;

    /** @test */
    public function it_can_show_expected_output()
    {
        $this->artisan('sample:command')
            ->expectsOutput('It works!')
            ->run();
    }
}
