<?php

namespace Orchestra\Testbench\Contracts\Attributes;

interface Resolvable
{
    /**
     * Resolve the actual attribute class.
     *
     * @return object|null
     */
    public function resolve(): ?object;
}
