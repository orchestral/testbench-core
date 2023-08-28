<?php

namespace Orchestra\Testbench\Tests\Foundation\Console\Actions;

use Illuminate\Console\View\Components\Factory as ComponentsFactory;
use Illuminate\Filesystem\Filesystem;
use Mockery as m;
use Orchestra\Testbench\Foundation\Console\Actions\GeneratesFile;
use Orchestra\Testbench\TestCase;

class GeneratesFileTest extends TestCase
{
    /** @test */
    public function it_can_generates_file()
    {
        $filesystem = m::mock(Filesystem::class);
        $components = m::mock(ComponentsFactory::class);

        $filesystem->shouldReceive('exists')->once()->with('a')->andReturnTrue()
            ->shouldReceive('exists')->once()->with('b')->andReturnFalse()
            ->shouldReceive('copy')->once()->with('a', 'b');

        $components->shouldReceive('task')->once()->with('File [b] generated');

        (new GeneratesFile(
            filesystem: $filesystem,
            components: $components,
        ))->handle('a', 'b');
    }

    /** @test */
    public function it_cannot_generates_file_when_file_already_generated()
    {
        $filesystem = m::mock(Filesystem::class);
        $components = m::mock(ComponentsFactory::class);

        $filesystem->shouldReceive('exists')->once()->with('a')->andReturnTrue()
            ->shouldReceive('exists')->once()->with('b')->andReturnTrue()
            ->shouldReceive('copy')->never()->with('a', 'b');

        $components->shouldReceive('twoColumnDetail')->once()->with('File [b] already exists', '<fg=yellow;options=bold>SKIPPED</>');

        (new GeneratesFile(
            filesystem: $filesystem,
            components: $components,
            force: false,
        ))->handle('a', 'b');
    }

    /** @test */
    public function it_can_generates_file_when_file_already_generated_using_force()
    {
        $filesystem = m::mock(Filesystem::class);
        $components = m::mock(ComponentsFactory::class);

        $filesystem->shouldReceive('exists')->once()->with('a')->andReturnTrue()
            ->shouldReceive('exists')->never()->with('b')
            ->shouldReceive('copy')->once()->with('a', 'b');

        $components->shouldReceive('task')->once()->with('File [b] generated');

        (new GeneratesFile(
            filesystem: $filesystem,
            components: $components,
            force: true,
        ))->handle('a', 'b');
    }

    /** @test */
    public function it_cannot_generates_file_when_source_file_does_not_exists()
    {
        $filesystem = m::mock(Filesystem::class);
        $components = m::mock(ComponentsFactory::class);

        $filesystem->shouldReceive('exists')->once()->with('a')->andReturnFalse()
            ->shouldReceive('exists')->never()->with('b')
            ->shouldReceive('copy')->never()->with('a', 'b');

        $components->shouldReceive('twoColumnDetail')->once()->with('Source file [a] doesn\'t exists', '<fg=yellow;options=bold>SKIPPED</>');

        (new GeneratesFile(
            filesystem: $filesystem,
            components: $components,
            force: true,
        ))->handle('a', 'b');
    }
}
