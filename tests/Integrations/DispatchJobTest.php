<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\TestCase;
use Workbench\App\Jobs\RegisterUser;

class DispatchJobTest extends TestCase
{
    /** @test */
    public function it_can_triggers_expected_jobs()
    {
        $this->expectsJobs(RegisterUser::class);

        dispatch(new RegisterUser());
    }
}
