<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Metadata\Annotation\Parser\Registry as MetadataAnnotationRegistry;
use PHPUnit\Runner\Version;
use PHPUnit\Util\Annotation\Registry as UtilAnnotationRegistry;

trait HandlesAnnotations
{
    /**
     * Parse test method annotations.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @param  string  $name
     */
    protected function parseTestMethodAnnotations($app, string $name): void
    {
        if (! $this instanceof TestCase) {
            return;
        }

        if (\class_exists(Version::class) && \version_compare(Version::id(), '10', '>=')) {
            $registry = MetadataAnnotationRegistry::getInstance();
        } else {
            $registry = UtilAnnotationRegistry::getInstance();
        }

        Collection::make(
            \rescue(function () use ($registry) {
                return $registry->forMethod(static::class, $this->getName(false))->symbolAnnotations();
            }, [], false)
        )->filter(function ($actions, $key) use ($name) {
            return $key === $name;
        })->each(function ($actions) use ($app) {
            Collection::make($actions ?? [])
                ->filter(function ($method) {
                    return ! \is_null($method) && \method_exists($this, $method);
                })->each(function ($method) use ($app) {
                    $this->{$method}($app);
                });
        });
    }
}
