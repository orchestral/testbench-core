<?php

namespace Orchestra\Testbench\Foundation\Events;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Illuminate\Console\View\Components\Factory;

class ServeCommandEnded
{
    /**
     * Construct a new event.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @param  \Illuminate\Console\View\Components\Factory  $components
     */
    public function __construct(
        public InputInterface $input,
        public OutputInterface $output,
        public Factory $components,
        public int $exitCode
    ) {
        //
    }
}
