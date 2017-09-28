<?php

namespace Orchestra\Testbench\Tests;

use Carbon\Carbon;
use Orchestra\Testbench\TestCase;

class TimezoneTest extends TestCase
{
    /**
     * Get application timezone.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return string|null
     */
    protected function getApplicationTimezone($app)
    {
        return 'Asia/Kuala_Lumpur';
    }

    /**
     * @test
     */
    public function testOverridingTimezone()
    {
        $this->assertEquals('Asia/Kuala_Lumpur', Carbon::now()->timezoneName);
    }
}
