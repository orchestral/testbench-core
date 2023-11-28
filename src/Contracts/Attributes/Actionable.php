<?php

namespace Orchestra\Testbench\Contracts\Attributes;

use Closure;

interface Actionable
{
    /**
     * Handle the attribute.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @param  \Closure():void  $action
     * @return mixed
     */
    public function handle($app, Closure $action);
}
