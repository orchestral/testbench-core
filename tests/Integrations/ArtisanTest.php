<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

use function Illuminate\Support\php_binary;
use function Orchestra\Testbench\package_path;
use function Orchestra\Testbench\remote;

class ArtisanTest extends TestCase
{
    #[Test]
    #[Group('core')]
    public function it_can_generate_the_same_output()
    {
        $phpBinary = \defined('PHP_BINARY') ? PHP_BINARY : php_binary();

        $remote = remote('--version --no-ansi')->mustRun();

        $artisan = (new Process(
            command: [$phpBinary, 'artisan', '--version', '--no-ansi'],
            cwd: package_path('laravel'),
        ))->mustRun();

        $this->assertSame(json_decode($artisan->getOutput(), true), json_decode($remote->getOutput(), true));
    }
}
