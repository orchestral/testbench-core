<?php

if (! file_exists(__DIR__.'/../vendor/autoload.php')) {
    require_once __DIR__.'/../vendor/autoload.php';

    return;
}

$workingPath = match (true) {
    defined('TESTBENCH_WORKING_PATH') => TESTBENCH_WORKING_PATH,
    is_string(getenv('TESTBENCH_WORKING_PATH')) => getenv('TESTBENCH_WORKING_PATH'),
    default => realpath(__DIR__.'/../'),
};

require_once $workingPath.'/vendor/autoload.php';
