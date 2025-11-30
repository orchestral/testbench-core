<?php

namespace Orchestra\Testbench\Tests\Functions;

use Illuminate\Console\Command;
use Orchestra\Testbench\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

use function Orchestra\Testbench\artisan;

class ArtisanTest extends TestCase
{
    #[Test]
    public function it_can_run_artisan_command()
    {
        $this->assertSame(Command::SUCCESS, artisan($this, 'env'));
        $this->assertSame(Command::SUCCESS, artisan($this->app, 'env'));
    }
}
