<?php

namespace Orchestra\Testbench\Tests\Workbench;

use Composer\InstalledVersions;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;

class DiscoversTest extends TestCase
{
    use InteractsWithViews;
    use WithWorkbench;

    /** @test */
    public function it_can_resolve_web_routes_from_discovers()
    {
        $this->get('/hello')
            ->assertOk();
    }

    /** @test */
    public function it_can_resolve_web_routes_using_macro_from_discovers()
    {
        $this->get('/hello-world')
            ->assertOk()
            ->assertSee('Hello world')
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
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
        $this->artisan('workbench:inspire')->assertExitCode(0);
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

    /** @test */
    public function it_can_discover_database_factories_from_model()
    {
        $this->assertSame(
            'Database\Factories\Illuminate\Foundation\Auh\UserFactory', Factory::resolveFactoryName('Illuminate\Foundation\Auh\User')
        );

        $this->assertSame(
            'Workbench\Database\Factories\UserFactory', Factory::resolveFactoryName('Workbench\App\Models\User')
        );
    }
}
