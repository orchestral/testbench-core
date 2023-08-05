<?php

namespace Orchestra\Testbench\Contracts;

use ArrayAccess;

interface Config extends ArrayAccess
{
    /**
     * Add additional service providers.
     *
     * @param  array<int, class-string<\Illuminate\Support\ServiceProvider>>  $providers
     * @return $this
     */
    public function addProviders(array $providers);

    /**
     * Get extra attributes.
     *
     * @return array{env: array, bootstrappers: array, providers: array, dont-discover: array}
     */
    public function getExtraAttributes(): array;

    /**
     * Get workbench attributes.
     *
     * @return array{start: string, user: string|int|null, guard: string|null}
     */
    public function getWorkbenchAttributes(): array;
}
