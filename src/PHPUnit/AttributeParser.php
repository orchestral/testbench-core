<?php

namespace Orchestra\Testbench\PHPUnit;

use Error;
use Orchestra\Testbench\Attributes\Define;
use ReflectionMethod;

class AttributeParser
{
    /**
     * Parse attribute for method.
     *
     * @param  string  $className
     * @param  string  $methodName
     * @return array
     */
    public static function forMethod(string $className, string $methodName): array
    {
        $attributes = [];

        foreach ((new ReflectionMethod($className, $methodName))->getAttributes() as $attribute) {
            if (
                ! str_starts_with($attribute->getName(), 'Orchestra\\Testbench\\Attributes\\')
                && ! str_starts_with($attribute->getName(), 'Orchestra\\Testbench\\Dusk\\Attributes\\')
            ) {
                continue;
            }

            try {
                $instance = $attribute->getName() === Define::class
                    ? transform($attribute->newInstance(), static function ($instance) {
                        /** @var \Orchestra\Testbench\Attributes\Define $instance */
                        return $instance->resolve();
                    }) : $attribute->newInstance();

                if (\is_null($instance)) {
                    continue;
                }

                $name = \get_class($instance);

                if (! isset($attributes[$name])) {
                    $attributes[$name] = [];
                }

                array_push($attributes[$name], $instance);
            } catch (Error $e) {
                //
            }
        }

        return $attributes;
    }
}
