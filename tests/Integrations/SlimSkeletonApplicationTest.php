<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Orchestra\Testbench\Attributes\DefineRoute;
use Orchestra\Testbench\Attributes\RequiresEnv;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

use function Illuminate\Filesystem\join_paths;
use function Orchestra\Testbench\workbench_path;

#[RequiresEnv('APP_KEY')]
class SlimSkeletonApplicationTest extends TestCase
{
    /**
     * Get application bootstrap file path.
     *
     * @return string
     */
    protected function getApplicationBootstrapFile()
    {
        return workbench_path(join_paths('bootstrap', 'app.php'));
    }

    #[Test]
    public function it_can_access_welcome_page_using_route_name()
    {
        $this->get(route('welcome'))
            ->assertOk();
    }

    #[Test]
    public function it_throws_exception_when_trying_to_access_authenticated_routes_as_guest_without_login_route_name()
    {
        $this->expectException(RouteNotFoundException::class);
        $this->expectExceptionMessage('Route [login] not defined.');

        $this->withoutExceptionHandling()
            ->get(route('dashboard'));
    }

    #[Test]
    #[DefineRoute('defineLoginRoutes')]
    public function it_can_be_redirected_to_login_route_name_when_trying_to_access_authenticated_routes()
    {
        $this->get(route('dashboard'))
            ->assertRedirectToRoute('login');
    }

    /**
     * Define login routes setup.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function defineLoginRoutes($router)
    {
        $router->get('/login', fn () => 'Login')->name('login');
    }
}
