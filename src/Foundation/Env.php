<?php

namespace Orchestra\Testbench\Foundation;

use Illuminate\Filesystem\Filesystem;
use Orchestra\Sidekick\UndefinedValue;
use RuntimeException;

/**
 * @api
 */
class Env extends \Illuminate\Support\Env
{
    /**
     * Determine if environmemt variable is available.
     *
     * @param  string  $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        return static::get($key, new UndefinedValue) instanceof UndefinedValue === false;
    }

    /**
     * Set an environment value.
     *
     * @param  string  $key
     * @param  string  $value
     * @return void
     */
    public static function set(string $key, string $value): void
    {
        static::getRepository()->set($key, $value);
    }

    /**
     * Forget an environment variable.
     *
     * @param  string  $key
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    public static function forget(string $key): bool
    {
        return static::getRepository()->clear($key);
    }

    /**
     * Forward environment value.
     *
     * @param  string  $key
     * @param  \Orchestra\Sidekick\UndefinedValue|mixed|null  $default
     * @return mixed
     */
    public static function forward(string $key, $default = null)
    {
        if (\func_num_args() === 1) {
            $default = new UndefinedValue;
        }

        $value = static::get($key, $default);

        if ($value instanceof UndefinedValue) {
            return false;
        }

        return static::encode($value);
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

    /**
     * Write an array of key-value pairs to the environment file.
     *
     * @param  array<string, mixed>  $variables
     * @param  string  $pathToFile
     * @param  bool  $overwrite
     * @return void
     *
     * @throws \RuntimeException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function writeVariables(array $variables, string $pathToFile, bool $overwrite = false): void
    {
        $filesystem = new Filesystem;

        if ($filesystem->missing($pathToFile)) {
            throw new RuntimeException("The file [{$pathToFile}] does not exist.");
        }

        $lines = explode(PHP_EOL, $filesystem->get($pathToFile));

        foreach ($variables as $key => $value) {
            $lines = self::addVariableToEnvContents($key, $value, $lines, $overwrite);
        }

        $filesystem->put($pathToFile, implode(PHP_EOL, $lines));
    }

    /**
     * Write a single key-value pair to the environment file.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  string  $pathToFile
     * @param  bool  $overwrite
     * @return void
     *
     * @throws \RuntimeException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function writeVariable(string $key, mixed $value, string $pathToFile, bool $overwrite = false): void
    {
        $filesystem = new Filesystem;

        if ($filesystem->missing($pathToFile)) {
            throw new RuntimeException("The file [{$pathToFile}] does not exist.");
        }

        $envContent = $filesystem->get($pathToFile);

        $lines = explode(PHP_EOL, $envContent);
        $lines = self::addVariableToEnvContents($key, $value, $lines, $overwrite);

        $filesystem->put($pathToFile, implode(PHP_EOL, $lines));
    }

    /**
     * Add a variable to the environment file contents.
     *
     * @laravel-overrides
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $envLines
     * @param  bool  $overwrite
     * @return array
     */
    protected static function addVariableToEnvContents(string $key, mixed $value, array $envLines, bool $overwrite): array
    {
        $prefix = explode('_', $key)[0].'_';
        $lastPrefixIndex = -1;

        $shouldQuote = preg_match('/^[a-zA-z0-9]+$/', $value) === 0;

        $lineToAddVariations = [
            $key.'='.(\is_string($value) ? '"'.addslashes($value).'"' : $value),
            $key.'='.(\is_string($value) ? "'".addslashes($value)."'" : $value),
            $key.'='.$value,
        ];

        $lineToAdd = $shouldQuote ? $lineToAddVariations[0] : $lineToAddVariations[2];

        if ($value === '') {
            $lineToAdd = $key.'=';
        }

        foreach ($envLines as $index => $line) {
            if (str_starts_with($line, $prefix)) {
                $lastPrefixIndex = $index;
            }

            if (\in_array($line, $lineToAddVariations)) {
                // This exact line already exists, so we don't need to add it again.
                return $envLines;
            }

            if ($line === $key.'=') {
                // If the value is empty, we can replace it with the new value.
                $envLines[$index] = $lineToAdd;

                return $envLines;
            }

            if (str_starts_with($line, $key.'=')) {
                if (! $overwrite) {
                    return $envLines;
                }

                $envLines[$index] = $lineToAdd;

                return $envLines;
            }
        }

        if ($lastPrefixIndex === -1) {
            if (\count($envLines) && $envLines[\count($envLines) - 1] !== '') {
                $envLines[] = '';
            }

            return array_merge($envLines, [$lineToAdd]);
        }

        return array_merge(
            \array_slice($envLines, 0, $lastPrefixIndex + 1),
            [$lineToAdd],
            \array_slice($envLines, $lastPrefixIndex + 1)
        );
    }
}
