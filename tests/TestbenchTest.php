<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Contracts\Bus\QueueingDispatcher;
use Illuminate\Queue\Queue;
use Orchestra\Testbench\TestCase;
use Workbench\App\Jobs\CustomPayloadJob;

class TestbenchTest extends TestCase
{
    /** @test */
    public function it_can_resolve_uses_testing_concerns()
    {
        $this->assertTrue(static::usesTestingConcern(\Orchestra\Testbench\Concerns\Testing::class));
        $this->assertFalse(static::usesTestingConcern(\Orchestra\Testbench\Concerns\WithWorkbench::class));
    }

    /**
     * @test
     *
     * @define-env registerCustomQueuePayload
     *
     * @dataProvider customQueuePayloadDataProvider
     */
    public function it_can_handle_custom_queue_payload()
    {
        $dispatcher = $this->app->make(QueueingDispatcher::class);

        $dispatcher->dispatchToQueue(new CustomPayloadJob());
    }

    protected function registerCustomQueuePayload($app)
    {
        $app->bind('one.time.password', function () {
            return random_int(1, 10);
        });

        Queue::createPayloadUsing(function () use ($app) {
            $password = $app->make('one.time.password');

            $app->offsetUnset('one.time.password');

            return ['password' => $password];
        });
    }

    public static function customQueuePayloadDataProvider()
    {
        yield ['laravel.com'];
        yield ['blog.laravel.com'];
    }
}
