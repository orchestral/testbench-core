<?php

namespace Orchestra\Testbench\Support;

class Env extends \Illuminate\Support\Env
{
    /**
     * Forward environment value.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function forward(string $key, $default)
    {
        $value = $this->get($key, $default);

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
