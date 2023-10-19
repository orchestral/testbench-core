<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Orchestra\Testbench\TestCase;

class CacheRouteTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        $this->defineCacheRoutes(<<<PHP
<?php

Route::get('stubs-controller', 'Workbench\App\Http\Controllers\ExampleController@index');
PHP);

        parent::setUp();
    }

    /** @test */
    public function it_can_cache_route()
    {
        $this->get('stubs-controller')
            ->assertOk()
            ->assertSee('ExampleController@index');
    }
}
