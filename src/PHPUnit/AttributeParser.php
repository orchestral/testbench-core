<?php

namespace Orchestra\Testbench\PHPUnit;

use Error;
use Orchestra\Testbench\Attributes\Define;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

class AttributeParser
{
    /**
     * Parse attribute for method.
     *
     * @param  class-string  $className
     * @param  string  $methodName
     * @return array
     */
    public static function forMethod(string $className, string $methodName): array
    {
        $attributes = [];

        foreach ((new ReflectionClass($className))->getAttributes() as $attribute) {
            if (! static::validAttribute($attribute)) {
                continue;
            }

            [$name, $instance] = static::resolveAttribute($attribute);

            if (! \is_null($name) && ! \is_null($instance)) {
                if (! isset($attributes[$name])) {
                    $attributes[$name] = [$instance];
                } else {
                    array_push($attributes[$name], $instance);
                }
            }
        }

        foreach ((new ReflectionMethod($className, $methodName))->getAttributes() as $attribute) {
            if (! static::validAttribute($attribute)) {
                continue;
            }

            [$name, $instance] = static::resolveAttribute($attribute);

            if (! \is_null($name) && ! \is_null($instance)) {
                if (! isset($attributes[$name])) {
                    $attributes[$name] = [$instance];
                } else {
                    array_push($attributes[$name], $instance);
                }
            }
        }

        return $attributes;
    }

    /**
     * Validate given attribute.
     *
     * @param  \ReflectionAttribute  $attribute
     * @return bool
     */
    protected static function validAttribute(ReflectionAttribute $attribute): bool
    {
        return str_starts_with($attribute->getName(), 'Orchestra\\Testbench\\Attributes\\')
            || str_starts_with($attribute->getName(), 'Orchestra\\Testbench\\Dusk\\Attributes\\');
    }

    /**
     * Resolve given attribute.
     *
     * @param  \ReflectionAttribute  $attribute
     * @return array{0: class-string|null, 1: object|null}
     */
    protected static function resolveAttribute(ReflectionAttribute $attribute): array
    {
        try {
            $instance = $attribute->getName() === Define::class
                ? transform($attribute->newInstance(), static function ($instance) {
                    /** @var \Orchestra\Testbench\Attributes\Define $instance */
                    return $instance->resolve();
                }) : $attribute->newInstance();

            if (\is_null($instance)) {
                return [null, null];
            }

            /** @var class-string $name */
            $name = \get_class($instance);

            return [$name, $instance];
        } catch (Error $e) {
            return [null, null];
        }
    }
}
