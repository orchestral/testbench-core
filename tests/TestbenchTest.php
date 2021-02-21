<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Contracts\Bus\QueueingDispatcher;
use Illuminate\Queue\Queue;
use Orchestra\Testbench\Tests\Fixtures\Jobs\CustomPayloadJob;

class TestbenchTest extends \Orchestra\Testbench\TestCase
{
    /**
     * @test
     * @define-env registerCustomQueuePayload
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

    public function customQueuePayloadDataProvider()
    {
        yield ['laravel.com'];
        yield ['blog.laravel.com'];
    }
}
