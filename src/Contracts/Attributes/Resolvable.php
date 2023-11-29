<?php

namespace Orchestra\Testbench\Contracts\Attributes;

interface Resolvable
{
    /**
     * Resolve the actual attribute class.
     *
     * @return \Orchestra\Testbench\Contracts\Attributes\Actionable|\Orchestra\Testbench\Contracts\Attributes\Invokable|null
     */
    public function resolve(): ?object;
}
