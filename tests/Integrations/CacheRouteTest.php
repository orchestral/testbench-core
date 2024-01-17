<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Illuminate\Support\Facades\Log;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

class CacheRouteTest extends TestCase
{
    /** {@inheritDoc} */
    #[\Override]
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

    #[Test]
    #[Group('without-parallel')]
    public function it_can_cache_route()
    {
        $this->get('stubs-controller')
            ->assertOk()
            ->assertSee('ExampleController@index');
    }

    #[Test]
    #[Group('without-parallel')]
    public function it_can_cache_closure_route()
    {
        Log::spy()->shouldReceive('info')->with('hello');

        $this->get('logger')
            ->assertOk();
    }
}
