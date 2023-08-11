<?php

namespace Orchestra\Testbench\Workbench;

class Composer extends \Illuminate\Support\Composer
{
    /**
     * Modify composer content.
     *
     * @param  callable(array):array  $callback
     * @return void
     */
    public function modify(callable $callback): void
    {
        $composerFile = "{$this->workingPath}/composer.json";

        $composer = json_decode((string) file_get_contents($composerFile), true, 512, JSON_THROW_ON_ERROR);

        $composer = \call_user_func($callback, $composer);

        file_put_contents(
            $composerFile,
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
    }
}
