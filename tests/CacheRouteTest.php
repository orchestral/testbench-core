<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\Concerns\InteractsWithPublishedFiles;
use Orchestra\Testbench\TestCase;

class CacheRouteTest extends TestCase
{
    use InteractsWithPublishedFiles;

    protected $files = [
        'routes/testbench.php',
        'bootstrap/cache/routes-v7.php',
    ];

    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        $this->tearDownInteractsWithPublishedFiles();

        parent::tearDown();
    }

    /** @test */
    public function it_can_cache_route()
    {
        $route = <<<PHP
<?php

Route::get('stubs-controller', 'Orchestra\Testbench\Tests\Fixtures\Controllers\Controller@index');
PHP;

        file_put_contents(base_path('routes/testbench.php'), $route);

        $this->artisan('route:cache')->run();

        $this->refreshApplicationWithCachedRoutes();

        $this->assertFilenameExists('routes/testbench.php');
        $this->assertFilenameExists('bootstrap/cache/routes-v7.php');

        $this->get('stubs-controller')
            ->assertOk()
            ->assertSee('Controller@index');
    }
}
