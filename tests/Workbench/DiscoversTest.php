<?php

namespace Orchestra\Testbench\Tests\Workbench;

use Composer\InstalledVersions;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;

#[WithConfig('app.key', 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF')]
class DiscoversTest extends TestCase
{
    use InteractsWithViews;
    use WithWorkbench;

    /** {@inheritDoc} */
    #[\Override]
    protected function setUp(): void
    {
        if (! \defined('LARAVEL_START')) {
            \define('LARAVEL_START', microtime(true));
        }

        parent::setUp();
    }

    #[Test]
    public function it_can_resolve_web_routes_from_discovers()
    {
        $this->get('/api/hello')
            ->assertOk();
    }

    #[Test]
    public function it_can_resolve_web_routes_using_macro_from_discovers()
    {
        $this->get('/hello-world')
            ->assertOk()
            ->assertSee('Hello world')
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
    }

    #[Test]
    public function it_can_resolve_health_check_from_discovers()
    {
        $this->get('/up')
            ->assertOk()
            ->assertSee('HTTP request received')
            ->assertSee('Response successfully rendered in');
    }

    #[Test]
    public function it_can_resolve_views_from_discovers()
    {
        $this->get('/testbench')
            ->assertOk()
            ->assertSee('Alert Component')
            ->assertSee('Notification Component');
    }

    #[Test]
    public function it_can_resolve_errors_views_from_discovers()
    {
        $this->get('/root')
            ->assertStatus(418)
            ->assertSeeText('I\'m a teapot')
            ->assertDontSeeText('412');
    }

    #[Test]
    public function it_can_resolve_route_name_from_discovers()
    {
        $this->assertSame(url('/testbench'), route('testbench'));
    }

    #[Test]
    public function it_can_resolve_commands_from_discovers()
    {
        $this->artisan('workbench:inspire')->assertOk();
    }

    #[Test]
    public function it_can_discover_config_files()
    {
        $this->assertSame(InstalledVersions::isInstalled('orchestra/workbench'), config('workbench.installed'));

        $this->assertSame(InstalledVersions::isInstalled('orchestra/workbench'), config('nested.workbench.installed'));
    }

    #[Test]
    public function it_can_discover_views_files()
    {
        $this->view('workbench::testbench')
            ->assertSee('Alert Component')
            ->assertSee('Notification Component');

        $this->view('testbench')
            ->assertSee('Alert Component')
            ->assertSee('Notification Component');
    }

    #[Test]
    public function it_can_discover_translation_files()
    {
        $this->assertSame('Good Morning', __('workbench::welcome.morning'));
    }

    #[Test]
    #[TestWith(['Workbench\\Database\\Factories\\Illuminate\\Foundation\\Auh\\UserFactory', 'Illuminate\\Foundation\\Auh\\User'])]
    #[TestWith(['Workbench\\Database\\Factories\\UserFactory', 'Workbench\\App\\Models\\User'])]
    public function it_can_discover_database_factories_from_model(string $factory, string $model)
    {
        $this->assertSame($factory, Factory::resolveFactoryName($model));
    }

    #[Test]
    public function it_can_discover_model_from_factory()
    {
        $this->assertSame('Workbench\App\Models\User', \Workbench\Database\Factories\UserFactory::new()->modelName());
    }
}
