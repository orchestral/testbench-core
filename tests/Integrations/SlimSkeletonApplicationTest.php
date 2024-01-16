<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;
use function Illuminate\Filesystem\join_paths;
use function Orchestra\Testbench\workbench_path;

class SlimSkeletonApplicationTest extends TestCase
{
    /**
     * Resolve application implementation.
     *
     * @return \Illuminate\Foundation\Application
     */
    protected function resolveApplication()
    {
        $app = require workbench_path(join_paths('bootstrap', 'app.php'));

        value($this->resolveApplicationResolvingCallback(), $app);

        return $app;
    }

    #[Test]
    public function it_can_access_welcome_page_using_route_name()
    {
        $response = $this->get(route('welcome'));

        $response->assertOk();
    }
}
