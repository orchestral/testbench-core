<?php 

namespace Orchestra\Testbench\View;

use Illuminate\View\FileViewFinder;

class InertiaViewFinder extends FileViewFinder
{
    /**
     * Register a view extension with the finder.
     *
     * @var string[]
     */
    protected $extensions = [
        'blade.php', 'php', 'css', 'html',
        'js', 'jsx', 'svelte', 'ts', 'tsx', 'vue',
    ];
}
