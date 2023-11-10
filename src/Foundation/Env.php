<?php

namespace Orchestra\Testbench\Foundation;

/**
 * @api
 */
class Env extends \Illuminate\Support\Env
{
    /**
     * Forward environment value.
     *
     * @param  string  $key
     * @param  mixed|null  $default
     * @return mixed
     */
    public static function forward(string $key, $default = null)
    {
        return static::encode(static::get($key, $default));
    }

    /**
     * Encode environment variable value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public static function encode($value)
    {
        if (\is_null($value)) {
            return '(null)';
        }

        if (\is_bool($value)) {
            return $value === true ? '(true)' : '(false)';
        }

        if (empty($value)) {
            return '(empty)';
        }

        return $value;
    }
}
