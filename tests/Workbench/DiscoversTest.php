<?php

namespace Orchestra\Testbench\Tests\Workbench;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;

class DiscoversTest extends TestCase
{
    use WithWorkbench;

    /** @test */
    public function it_can_discover_web_routes()
    {
        $this->get('workbench-route-1')
            ->assertOk()
            ->assertSee('Orchestra Testbench');
    }
}
