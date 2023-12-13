<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Illuminate\Support\Facades\Log;
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

use Psr\Log\LoggerInterface;

Route::get('stubs-controller', 'Workbench\App\Http\Controllers\ExampleController@index');

Route::any('/logger', function (LoggerInterface \$log) {
    \$log->info('hello');
})->where(['all' => '.*']);
PHP);

        parent::setUp();
    }

    /**
     * @test
     *
     * @group without-parallel
     */
    public function it_can_cache_route()
    {
        $this->get('stubs-controller')
            ->assertOk()
            ->assertSee('ExampleController@index');
    }

    /**
     * @test
     *
     * @group without-parallel
     */
    public function it_can_cache_closure_route()
    {
        Log::spy()->shouldReceive('info')->with('hello');

        $this->get('logger')
            ->assertOk();
    }
}
