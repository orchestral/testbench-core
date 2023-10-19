<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Illuminate\Console\Application as Artisan;
use Orchestra\Testbench\TestCase;
use Workbench\App\Console\Commands\DummyCommand;

class CommandTest extends TestCase
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
