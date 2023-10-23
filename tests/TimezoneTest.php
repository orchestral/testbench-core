<?php

namespace Orchestra\Testbench\Tests;

use Carbon\Carbon;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TimezoneTest extends TestCase
{
    /**
     * Get application timezone.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return string|null
     */
    protected function getApplicationTimezone($app)
    {
        return 'Asia/Kuala_Lumpur';
    }

    #[Test]
    public function it_can_override_timezone()
    {
        $this->assertEquals('Asia/Kuala_Lumpur', Carbon::now()->timezoneName);
    }
}
