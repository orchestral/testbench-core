<?php

namespace Orchestra\Testbench\Support;

/**
 * @internal
 */
final readonly class UndefinedValue implements \JsonSerializable
{
    /**
     * Determine if value is equivalent to "undefined" or "null".
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function equalsTo($value): bool
    {
        return $value instanceof UndefinedValue || \is_null($value);
    }

    /**
     * Get the value as json.
     *
     * @return null
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return null;
    }
}
