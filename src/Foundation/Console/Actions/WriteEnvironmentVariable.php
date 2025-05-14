<?php

namespace Orchestra\Testbench\Foundation\Console\Actions;

use Illuminate\Console\View\Components\Factory as ComponentsFactory;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;

/**
 * @internal
 */
final class WriteEnvironmentVariable
{
    /**
     * Construct a new action instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  \Illuminate\Console\View\Components\Factory|null  $components
     * @param  bool  $force
     * @param  string|null  $workingPath
     */
    public function __construct(
        public Filesystem $filesystem,
        public ?ComponentsFactory $components = null,
        public bool $force = false,
        public ?string $workingPath = null
    ) {}

    /**
     * Handle the action.
     *
     * @param  array<string, mixed>  $variables
     * @param  string|false|null  $file
     * @param  bool  $overwrite
     * @return void
     *
     * @throws \RuntimeException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle(array $variables, string|false|null $filename, bool $overwrite = false): void
    {
        if (! \is_string($file)) {
            return;
        }

        $this->writeVariables($variables, $filename, $overwrite);
    }

    /**
     * Write an array of key-value pairs to the environment file.
     *
     * @laravel-overrides
     *
     * @param  array<string, mixed>  $variables
     * @param  string|false|null  $file
     * @param  bool  $overwrite
     * @return void
     *
     * @throws \RuntimeException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function writeVariables(array $variables, string $pathToFile, bool $overwrite = false): void
    {
        $filesystem = new Filesystem;

        if ($this->filesystem->missing($pathToFile)) {
            throw new RuntimeException("The file [{$pathToFile}] does not exist.");
        }

        $lines = explode(PHP_EOL, $this->filesystem->get($pathToFile));

        foreach ($variables as $key => $value) {
            $lines = $this->addVariableToEnvContents($key, $value, $lines, $overwrite);
        }

        $this->filesystem->put($pathToFile, implode(PHP_EOL, $lines));
    }

    /**
     * Write a single key-value pair to the environment file.
     *
     * @laravel-overrides
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
    protected function writeVariable(string $key, mixed $value, string $pathToFile, bool $overwrite = false): void
    {
        if ($this->filesystem->missing($pathToFile)) {
            throw new RuntimeException("The file [{$pathToFile}] does not exist.");
        }

        $envContent = $this->filesystem->get($pathToFile);

        $lines = explode(PHP_EOL, $envContent);
        $lines = $this->addVariableToEnvContents($key, $value, $lines, $overwrite);

        $this->filesystem->put($pathToFile, implode(PHP_EOL, $lines));
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
    protected function addVariableToEnvContents(string $key, mixed $value, array $envLines, bool $overwrite): array
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
