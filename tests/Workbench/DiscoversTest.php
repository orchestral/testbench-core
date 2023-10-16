<?php

namespace Orchestra\Testbench\Tests\Workbench;

use Composer\InstalledVersions;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;

class DiscoversTest extends TestCase
{
    use WithWorkbench;

    protected function setUp(): void
    {
        if (! InstalledVersions::isInstalled('orchestra/workbench')) {
            $this->markTestSkipped('Requires `orchestra/workbench`');
        }

        parent::setUp();
    }

    /** @test */
    public function it_can_resolve_web_routes_from_discovers()
    {
        $this->get('/hello')
            ->assertOk();
    }

    /** @test */
    public function it_can_resolve_commands_from_discovers()
    {
        $this->artisan('workbench:inspire')->assertOk();
    }
}
