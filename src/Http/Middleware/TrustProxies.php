<?php

namespace Orchestra\Testbench\Http\Middleware;

use Fideloper\Proxy\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array|string
     */
    protected array|string $proxies = [];

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected int $headers = Request::HEADER_X_FORWARDED_ALL;
}
