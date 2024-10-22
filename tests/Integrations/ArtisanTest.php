<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
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
        $remote = remote('--version --no-ansi')->mustRun();

        $artisan = (new Process(
            command: [php_binary(), 'artisan', '--version', '--no-ansi'],
            cwd: package_path('laravel'),
            env: ['TESTBENCH_WORKING_PATH' => package_path()],
        ))->mustRun();

        $this->assertSame(json_decode($artisan->getOutput(), true), json_decode($remote->getOutput(), true));
    }
}
