<?php

namespace Orchestra\Testbench\Tests\Databases;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

#[WithConfig('database.default', 'testing')]
class MigrateWithRealpathTest extends TestCase
{
    /** {@inheritDoc} */
    #[\Override]
    protected function defineDatabaseMigrations()
    {
        // call migrations specific to our tests, e.g. to seed the db
        // the path option should be an absolute path.
        $this->loadMigrationsFrom(realpath(__DIR__.'/../../workbench/database/migrations'));
    }

    #[Test]
    public function it_runs_the_migrations()
    {
        $users = DB::table('testbench_users')->where('id', '=', 1)->first();

        $this->assertEquals('crynobone@gmail.com', $users->email);
        $this->assertTrue(Hash::check('123', $users->password));
    }
}
