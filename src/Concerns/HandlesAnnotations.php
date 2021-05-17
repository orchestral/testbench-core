<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Metadata\Annotation\Parser\Registry as AnnotationRegistry;
use PHPUnit\Runner\Version;
use PHPUnit\Util\Test as TestUtil;

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
            $annotations = \rescue(function () {
                return AnnotationRegistry::getInstance()->forMethod(static::class, $this->getName(false))->symbolAnnotations();
            }, [], false);

            Collection::make($annotations)->filter(function ($location, $key) use ($name) {
                return $key === $name;
            })->each(function ($location) use ($app) {
                Collection::make($location ?? [])
                    ->filter(function ($method) {
                        return ! \is_null($method) && \method_exists($this, $method);
                    })->each(function ($method) use ($app) {
                        $this->{$method}($app);
                    });
            });

            return;
        }

        $annotations = TestUtil::parseTestMethodAnnotations(
            static::class, $this->getName(false)
        );

        Collection::make($annotations)->each(function ($location) use ($name, $app) {
            Collection::make($location[$name] ?? [])
                ->filter(function ($method) {
                    return ! \is_null($method) && \method_exists($this, $method);
                })->each(function ($method) use ($app) {
                    $this->{$method}($app);
                });
        });
    }
}
