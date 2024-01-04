<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Orchestra\Testbench\TestCase;

class InlineCacheRouteTest extends TestCase
{
    /**
     * @test
     *
     * @group without-parallel
     */
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
