<?php

if (! defined('TESTBENCH_WORKING_PATH') && is_string(getenv('TESTBENCH_WORKING_PATH'))) {
    define('TESTBENCH_WORKING_PATH', getenv('TESTBENCH_WORKING_PATH'));
}

$workingPath = defined('TESTBENCH_WORKING_PATH') ? TESTBENCH_WORKING_PATH : realpath(__DIR__.'/../');

require_once $workingPath.'/vendor/autoload.php';
