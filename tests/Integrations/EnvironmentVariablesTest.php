<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Foundation\Auth\User;
use Orchestra\Testbench\Factories\UserFactory;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

class EnvironmentVariablesTest extends TestCase
{
    /** {@inheritDoc} */
    #[\Override]
    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'testing');
    }

    /** {@inheritDoc} */
    #[\Override]
    protected function defineDatabaseMigrations()
    {
        $this->loadLaravelMigrations(['--database' => 'testing']);
    }

    #[Test]
    #[Group('commander')]
    public function it_can_be_used_without_having_an_environment_variables_file()
    {
        $user = UserFactory::new()->create();

        $this->assertFalse(file_exists(realpath(__DIR__.'/../../laravel/.env')));
        $this->assertFalse(file_exists(base_path('./env')));

        $this->assertInstanceOf(User::class, $user);
    }
}
