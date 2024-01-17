<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Carbon\CarbonImmutable;
use Illuminate\Support\DateFactory;
use Orchestra\Testbench\Contracts\Attributes\Invokable as InvokableContract;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class WithImmutableDates implements InvokableContract
{
    /**
     * Handle the attribute.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return \Closure|null
     */
    public function __invoke($app)
    {
        DateFactory::use(CarbonImmutable::class);
    }
}
