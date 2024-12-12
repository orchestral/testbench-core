<?php

namespace Orchestra\Testbench\Tests\Foundation\Console;

use Orchestra\Testbench\Foundation\Console\TerminatingConsole;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TerminatingConsoleTest extends TestCase
{
    #[Test]
    public function it_can_handle_terminating_callbacks_on_terminal()
    {
        $this->assertFalse(isset($_SERVER['TerminatingConsole.before']));
        $this->assertFalse(isset($_SERVER['TerminatingConsole.beforeWhenTrue']));
        $this->assertFalse(isset($_SERVER['TerminatingConsole.beforeWhenFalse']));

        TerminatingConsole::before(function () {
            $_SERVER['TerminatingConsole.before'] = true;
        });

        TerminatingConsole::beforeWhen(true, function () {
            $_SERVER['TerminatingConsole.beforeWhenTrue'] = true;
        });

        TerminatingConsole::beforeWhen(false, function () {
            $_SERVER['TerminatingConsole.beforeWhenFalse'] = true;
        });

        TerminatingConsole::handle();

        $this->assertTrue(isset($_SERVER['TerminatingConsole.before']));
        $this->assertTrue(isset($_SERVER['TerminatingConsole.beforeWhenTrue']));
        $this->assertFalse(isset($_SERVER['TerminatingConsole.beforeWhenFalse']));

        unset(
            $_SERVER['TerminatingConsole.before'],
            $_SERVER['TerminatingConsole.beforeWhenTrue'],
            $_SERVER['TerminatingConsole.beforeWhenFalse'],
        );

        TerminatingConsole::flush();
    }
}
