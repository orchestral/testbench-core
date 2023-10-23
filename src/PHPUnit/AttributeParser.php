<?php

namespace Orchestra\Testbench\PHPUnit;

use Error;
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
                $instance = $attribute->newInstance();

                if (! isset($attributes[$attribute->getName()])) {
                    $attributes[$attribute->getName()] = [];
                }

                array_push($attributes[$attribute->getName()], $instance);
            } catch (Error $e) {
                //
            }
        }

        return $attributes;
    }
}
