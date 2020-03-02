<?php

namespace Orchestra\Testbench\Console\Commands;

use Orchestra\Testbench\TestRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle as OutputStyle;

class TestRunnerCommand extends Command
{
    /**
     * Test working path.
     *
     * @var string
     */
    protected $workingPath;

    /**
     * Create a new command instance.
     *
     * @param  string  $workingPath
     */
    public function __construct(string $workingPath)
    {
        $this->workingPath = $workingPath;

        parent::__construct();
    }

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();

        $this->setName('run')
            ->setDescription('Run the tests.')
            ->addOption('without-tty', null, InputOption::VALUE_NONE, 'Disable output to TTY.');
    }

    /**
     * Execute the command.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     *
     * @return int 0 if everything went fine, or an exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $withoutTty = $input->getOption('without-tty');

        $options = \array_slice($_SERVER['argv'], $withoutTty ? 3 : 2);

        $runner = new TestRunner(
            new OutputStyle($input, $output), $this->workingPath, $withoutTty
        );

        return $runner($options);
    }
}
