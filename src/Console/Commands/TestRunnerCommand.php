<?php

namespace Orchestra\Testbench\Console\Commands;

use Illuminate\Console\Concerns\HasParameters;
use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestRunner;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Process;

class TestRunnerCommand extends Command
{
    use HasParameters,
        InteractsWithIO;

    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the package tests';

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

        $this->setName($this->name)
            ->setDescription($this->description);

        $this->specifyParameters();
    }

     /**
     * Run the console command.
     *
     * @return int
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->setOutput(new OutputStyle($input, $output));

        return parent::run(
            $this->input = $input, $this->output
        );
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
        $options = \array_slice($_SERVER['argv'], $this->option('without-tty') ? 3 : 2);

        $runner = new TestRunner(
            $this->output, $this->workingPath, $this->option('without-tty')
        );

        return $runner($options);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['without-tty', null, InputOption::VALUE_NONE, 'Disable output to TTY.'],
        ];
    }
}
