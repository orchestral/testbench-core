<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class StashRouteTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        $this->defineStashRoutes(function () {
            Route::get('stubs-controller', 'Workbench\App\Http\Controllers\ExampleController@index');
        });

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
