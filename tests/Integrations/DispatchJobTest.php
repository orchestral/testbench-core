<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Support\Facades\Bus;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Workbench\App\Jobs\RegisterUser;

class DispatchJobTest extends TestCase
{
    #[Test]
    public function it_can_triggers_expected_jobs()
    {
        Bus::fake();

        dispatch(new RegisterUser());

        Bus::assertDispatched(RegisterUser::class);
    }
}
