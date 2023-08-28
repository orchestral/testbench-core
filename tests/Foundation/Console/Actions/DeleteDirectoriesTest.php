<?php

namespace Orchestra\Testbench\Foundation\Console\Actions;

use Illuminate\Console\View\Components\Factory as ComponentsFactory;
use Illuminate\Filesystem\Filesystem;
use Mockery as m;
use Orchestra\Testbench\TestCase;

class DeleteDirectoriesTest extends TestCase
{
    /** @test */
    public function it_can_ensure_directory_exists()
    {
        $filesystem = m::mock(Filesystem::class);
        $components = m::mock(ComponentsFactory::class);

        $filesystem->shouldReceive('isDirectory')->once()->with('a')->andReturnTrue()
            ->shouldReceive('deleteDirectory')->once()->with('a')->andReturnSelf()
            ->shouldReceive('isDirectory')->once()->with('b')->andReturnFalse()
            ->shouldReceive('deleteDirectory')->never()->with('b')->andReturnSelf()
            ->shouldReceive('isDirectory')->once()->with('c/d')->andReturnTrue()
            ->shouldReceive('deleteDirectory')->once()->with('c/d')->andReturnSelf();

        $components->shouldReceive('task')->once()->with('Directory [a] has been deleted')->andReturnNull()
            ->shouldReceive('twoColumnDetail')->once()->with('Directory [b] doesn\'t exists', '<fg=yellow;options=bold>SKIPPED</>')->andReturnNull()
            ->shouldReceive('task')->once()->with('Directory [c/d] has been deleted')->andReturnNull();

        (new DeleteDirectories(
            filesystem: $filesystem,
            components: $components,
        ))->handle(['a', 'b', 'c/d']);
    }
}
