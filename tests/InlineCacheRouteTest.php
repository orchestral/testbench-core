<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\Concerns\InteractsWithPublishedFiles;
use Orchestra\Testbench\TestCase;

class InlineCacheRouteTest extends TestCase
{
    use InteractsWithPublishedFiles;

    protected $files = [
        'routes/testbench.php',
        'bootstrap/cache/routes-v7.php',
        '.laravel/cache/routes-v7.php',
    ];

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        $this->beforeApplicationDestroyed(function () {
            $this->tearDownInteractsWithPublishedFiles();
        });

        parent::setUp();
    }

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
