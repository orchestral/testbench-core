<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Orchestra\Testbench\TestCase;

class InlineCacheRouteTest extends TestCase
{
    /** @test */
    public function it_can_cache_route()
    {
        $this->defineCacheRoutes(<<<PHP
<?php

Route::get('stubs-controller', 'Orchestra\Testbench\Tests\Fixtures\Controllers\Controller@index');
PHP);

        $this->get('stubs-controller')
            ->assertOk()
            ->assertSee('Controller@index');
    }
}
