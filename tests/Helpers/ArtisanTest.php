<?php

namespace Orchestra\Testbench\Tests\Helpers;

use Illuminate\Console\Command;
use Orchestra\Testbench\TestCase;

use function Orchestra\Testbench\artisan;

class ArtisanTest extends TestCase
{
    /** @test */
    public function it_can_run_artisan_command()
    {
        $this->assertSame(Command::SUCCESS, artisan($this, 'env'));
        $this->assertSame(Command::SUCCESS, artisan($this->app, 'env'));
    }
}
