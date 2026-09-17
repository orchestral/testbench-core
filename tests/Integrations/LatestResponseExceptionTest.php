<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;
use SessionHandlerInterface;

#[WithConfig('app.debug', false)]
class LatestResponseExceptionTest extends TestCase
{
    #[Group('without-parallel')]
    public function testItRendersAuthorizationExceptions()
    {
        Route::get('test-route', fn () => Response::deny('expected message', 321)->authorize());

        // HTTP request...
        $this->usingSafeSessionDriver(function () {
            $this->get('test-route')
                ->assertStatus(403)
                ->assertSeeText('expected message');
        });

        // JSON request...
        $this->getJson('test-route')
            ->assertStatus(403)
            ->assertExactJson([
                'message' => 'expected message',
            ]);
    }

    #[Group('without-parallel')]
    public function testItRendersAuthorizationExceptionsWithCustomStatusCode()
    {
        Route::get('test-route', fn () => Response::deny('expected message', 321)->withStatus(404)->authorize());

        // HTTP request...
        $this->usingSafeSessionDriver(function () {
            $this->get('test-route')
                ->assertStatus(404)
                ->assertSeeText('Not Found');
        });

        // JSON request...
        $this->getJson('test-route')
            ->assertStatus(404)
            ->assertExactJson([
                'message' => 'expected message',
            ]);
    }

    #[Group('without-parallel')]
    public function testItRendersAuthorizationExceptionsWithStatusCodeTextWhenNoMessageIsSet()
    {
        Route::get('test-route', fn () => Response::denyWithStatus(404)->authorize());

        // HTTP request...
        $this->usingSafeSessionDriver(function () {
            $this->get('test-route')
                ->assertStatus(404)
                ->assertSeeText('Not Found');
        });

        // JSON request...
        $this->getJson('test-route')
            ->assertStatus(404)
            ->assertExactJson([
                'message' => 'Not Found',
            ]);

        Route::get('test-route', fn () => Response::denyWithStatus(418)->authorize());

        // HTTP request...
        $this->usingSafeSessionDriver(function () {
            $this->get('test-route')
                ->assertStatus(418)
                ->assertSeeText("I'm a teapot", false);
        });

        // JSON request...
        $this->getJson('test-route')
            ->assertStatus(418)
            ->assertExactJson([
                'message' => "I'm a teapot",
            ]);
    }

    #[Group('without-parallel')]
    public function testItRendersAuthorizationExceptionsWithStatusButWithoutResponse()
    {
        Route::get('test-route', fn () => throw (new AuthorizationException)->withStatus(418));

        // HTTP request...
        $this->usingSafeSessionDriver(function () {
            $this->get('test-route')
                ->assertStatus(418)
                ->assertSeeText("I'm a teapot", false);
        });

        // JSON request...
        $this->getJson('test-route')
            ->assertStatus(418)
            ->assertExactJson([
                'message' => "I'm a teapot",
            ]);
    }

    #[Group('without-parallel')]
    public function testItHasFallbackErrorMessageForUnknownStatusCodes()
    {
        Route::get('test-route', fn () => throw (new AuthorizationException)->withStatus(399));

        // HTTP request...
        $this->usingSafeSessionDriver(function () {
            $this->get('test-route')
                ->assertStatus(399)
                ->assertSeeText('Whoops, looks like something went wrong.');
        });

        // JSON request...
        $this->getJson('test-route')
            ->assertStatus(399)
            ->assertExactJson([
                'message' => 'Whoops, looks like something went wrong.',
            ]);
    }

    protected function usingSafeSessionDriver(callable $callback): void
    {
        $session = $this->app['session'];
        $defaultDriver = $session->getDefaultDriver();
        $storeResolved = $this->app->resolved('session.store');
        $originalStore = $storeResolved ? $this->app->make('session.store') : null;
        $driver = 'php86-safe';

        $session->setDefaultDriver($driver);
        $session->forgetDrivers();

        $store = $session->driver();
        $store->start();

        $this->app->instance('session.store', $store);

        try {
            $callback();
        } finally {
            $session->setDefaultDriver($defaultDriver);
            $session->forgetDrivers();

            if ($storeResolved && $originalStore !== null) {
                $this->app->instance('session.store', $originalStore);
            } else {
                $this->app->forgetInstance('session.store');
            }
        }
    }
}
