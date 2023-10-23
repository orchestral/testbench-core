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

        if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
            foreach ((new ReflectionMethod($className, $methodName))->getAttributes() as $attribute) {
                if (
                    ! str_starts_with($attribute->getName(), 'Orchestra\\Testbench\\Attributes\\')
                    && ! str_starts_with($attribute->getName(), 'Orchestra\\Testbench\\Dusk\\Attributes\\')
                ) {
                    continue;
                }

                try {
                    if ($attribute->getName() === Define::class) {
                        $instance = $attribute->newInstance()->resolve();
                    } else {
                        $instance = $attribute->newInstance();
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
        }

        return $attributes;
    }
}
