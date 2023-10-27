<?php

namespace Orchestra\Testbench\Foundation\Console\Actions;

use Illuminate\Console\View\Components\Factory as ComponentsFactory;
use Illuminate\Filesystem\Filesystem;
use Mockery as m;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DeleteFilesTest extends TestCase
{
    #[Test]
    public function it_can_ensure_directory_exists()
    {
        $filesystem = m::mock(Filesystem::class);
        $components = m::mock(ComponentsFactory::class);

        $filesystem->shouldReceive('exists')->once()->with('a')->andReturnTrue()
            ->shouldReceive('delete')->once()->with('a')->andReturnSelf()
            ->shouldReceive('exists')->once()->with('b')->andReturnFalse()
            ->shouldReceive('delete')->never()->with('b')->andReturnSelf()
            ->shouldReceive('exists')->once()->with('c/d')->andReturnTrue()
            ->shouldReceive('delete')->once()->with('c/d')->andReturnSelf();

        $components->shouldReceive('task')->once()->with('File [a] has been deleted')->andReturnNull()
            ->shouldReceive('twoColumnDetail')->once()->with('File [b] doesn\'t exists', '<fg=yellow;options=bold>SKIPPED</>')->andReturnNull()
            ->shouldReceive('task')->once()->with('File [c/d] has been deleted')->andReturnNull();

        (new DeleteFiles(
            filesystem: $filesystem,
            components: $components,
        ))->handle(['a', 'b', 'c/d']);
    }
}
