<?php

namespace Orchestra\Testbench\Foundation\Console\Actions;

/**
 * @api
 */
abstract class Action
{
    /**
     * Determine if action should only be pretended.
     */
    protected bool $pretending = false;
}
