<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Database\Eloquent\Factory as ModelFactory;

trait WithFactories
{
    /**
     * Load model factories from path.
     *
     * @param  string  $path
     *
     * @return $this
     */
    protected function withFactories(string $path): self
    {
        $this->app->make(ModelFactory::class)->load($path);

        return $this;
    }
}
