<?php

namespace Orchestra\Testbench\Contracts\Attributes;

interface Resolvable
{
    /**
     * Resolve the actual attribute class.
     *
     * @return \Orchestra\Testbench\Contracts\Attributes\TestingFeature|null
     */
    public function resolve(): ?object;
}
