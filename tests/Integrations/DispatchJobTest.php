<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Support\Facades\Bus;
use Orchestra\Testbench\TestCase;
use Orchestra\Testbench\Tests\Fixtures\Jobs\RegisterUser;

class DispatchJobTest extends TestCase
{
    /** @test */
    public function it_can_triggers_expected_jobs()
    {
        Bus::fake();

        dispatch(new RegisterUser());

        Bus::assertDispatched(RegisterUser::class);
    }
}
