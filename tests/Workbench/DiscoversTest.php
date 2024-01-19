<?php

namespace Orchestra\Testbench\Tests\Workbench;

use Composer\InstalledVersions;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;

class DiscoversTest extends TestCase
{
    use InteractsWithViews;
    use WithWorkbench;

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        $app['config']->set(['app.key' => 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF']);
    }

    /** @test */
    public function it_can_resolve_web_routes_from_discovers()
    {
        $this->get('/hello')
            ->assertOk();
    }

    /** @test */
    public function it_can_resolve_views_from_discovers()
    {
        $this->get('/testbench')
            ->assertOk()
            ->assertSee('Alert Component')
            ->assertSee('Notification Component');
    }

    /** @test */
    public function it_can_resolve_route_name_from_discovers()
    {
        $this->assertSame(url('/testbench'), route('testbench'));
    }

    /** @test */
    public function it_can_resolve_commands_from_discovers()
    {
        $this->artisan('workbench:inspire')->assertOk();
    }

    /** @test */
    public function it_can_discover_config_files()
    {
        $this->assertSame(InstalledVersions::isInstalled('orchestra/workbench'), config('workbench.installed'));

        $this->assertSame(InstalledVersions::isInstalled('orchestra/workbench'), config('nested.workbench.installed'));
    }

    /** @test */
    public function it_can_discover_views_files()
    {
        $this->view('workbench::testbench')
            ->assertSee('Alert Component')
            ->assertSee('Notification Component');

        $this->view('testbench')
            ->assertSee('Alert Component')
            ->assertSee('Notification Component');
    }

    /** @test */
    public function it_can_discover_translation_files()
    {
        $this->assertSame('Good Morning', __('workbench::welcome.morning'));
    }
}
