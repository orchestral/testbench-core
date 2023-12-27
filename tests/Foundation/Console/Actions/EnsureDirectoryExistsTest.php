<?php

namespace Orchestra\Testbench\Foundation\Console\Actions;

use Illuminate\Console\View\Components\Factory as ComponentsFactory;
use Illuminate\Filesystem\Filesystem;
use Mockery as m;
use Orchestra\Testbench\TestCase;

use function Illuminate\Filesystem\join_paths;

class EnsureDirectoryExistsTest extends TestCase
{
    /** @test */
    public function it_can_ensure_directory_exists()
    {
        $filesystem = m::mock(Filesystem::class);
        $components = m::mock(ComponentsFactory::class);

        $filesystem->shouldReceive('isDirectory')->once()->with('a')->andReturnFalse()
            ->shouldReceive('ensureDirectoryExists')->once()->with('a', 493, true)->andReturnSelf()
            ->shouldReceive('copy')->once()->with(M::type('String'), join_paths('a', '.gitkeep'))->andReturnSelf()
            ->shouldReceive('isDirectory')->once()->with('b')->andReturnTrue()
            ->shouldReceive('ensureDirectoryExists')->never()->with('b', 493, true)->andReturnSelf()
            ->shouldReceive('copy')->never()->with(M::type('String'), join_paths('b', '.gitkeep'))->andReturnSelf()
            ->shouldReceive('isDirectory')->once()->with(join_paths('c', 'd'))->andReturnFalse()
            ->shouldReceive('ensureDirectoryExists')->once()->with(join_paths('c', 'd'), 493, true)->andReturnSelf()
            ->shouldReceive('copy')->once()->with(M::type('String'), join_paths('c', 'd', '.gitkeep'))->andReturnSelf();

        $components->shouldReceive('task')->once()->with('Prepare [a] directory')->andReturnNull()
            ->shouldReceive('twoColumnDetail')->once()->with('Directory [b] already exists', '<fg=yellow;options=bold>SKIPPED</>')->andReturnNull()
            ->shouldReceive('task')->once()->with(sprintf('Prepare [%s] directory', join_paths('c', 'd')))->andReturnNull();

        (new EnsureDirectoryExists(
            filesystem: $filesystem,
            components: $components,
        ))->handle(['a', 'b', join_paths('c', 'd')]);
    }
}
