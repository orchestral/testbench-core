<?php

namespace Orchestra\Testbench\Tests\Databases;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

use function Orchestra\Testbench\artisan;

#[WithConfig('database.default', 'testing')]
class MigrateDatabaseWithoutMockedConsoleTest extends TestCase
{
    use WithWorkbench;

    /** {@inheritDoc} */
    #[\Override]
    protected function defineDatabaseMigrations()
    {
        artisan($this, 'migrate', ['--database' => 'testing']);
    }

    #[Test]
    public function it_runs_the_migrations_without_mocked_console()
    {
        $users = DB::table('testbench_users')->where('id', '=', 1)->first();

        $this->assertEquals('crynobone@gmail.com', $users->email);
        $this->assertTrue(Hash::check('123', $users->password));

        $this->assertEquals([
            'id',
            'email',
            'password',
            'created_at',
            'updated_at',
        ], Schema::getColumnListing('testbench_users'));
    }
}
