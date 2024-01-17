<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Illuminate\Console\Application as Artisan;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Workbench\App\Console\Commands\DummyCommand;

class CommandTest extends TestCase
{
    /** {@inheritDoc} */
    #[\Override]
    protected function setUp(): void
    {
        Artisan::starting(function ($artisan) {
            $artisan->resolveCommands([DummyCommand::class]);
        });

        parent::setUp();
    }

    #[Test]
    public function it_can_show_expected_output()
    {
        $this->artisan('sample:command')
            ->expectsOutput('It works!')
            ->run();
    }
}
