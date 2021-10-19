<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Console\Application as Artisan;
use Orchestra\Testbench\Tests\Fixtures\Commands\DummyCommand;

class CommandTest extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        Artisan::starting(function ($artisan) {
            $artisan->resolveCommands([DummyCommand::class]);
        });

        parent::setUp();
    }

    /** @test */
    public function it_can_show_expected_output()
    {
        $this->artisan('sample:command')
            ->expectsOutput('It works!')
            ->run();
    }
}
