<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Orchestra\Testbench\Tests\TestCase;

class InlineCacheRouteTest extends TestCase
{
    /** @test */
    public function it_can_cache_route()
    {
        $this->defineCacheRoutes(<<<PHP
<?php

Route::get('stubs-controller', 'Workbench\App\Http\Controllers\ExampleController@index');
PHP);

        $this->get('stubs-controller')
            ->assertOk()
            ->assertSee('ExampleController@index');
    }
}
