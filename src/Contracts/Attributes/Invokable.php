<?php

namespace Orchestra\Testbench\Contracts\Attributes;

interface Invokable
{
    /**
     * Handle the attribute.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return mixed
     */
    public function __invoke($app);
}
