<?php

namespace Orchestra\Testbench\Tests\Databases;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Orchestra\Testbench\Attributes\DefineDatabase;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

use function Orchestra\Testbench\after_resolving;
use function Orchestra\Testbench\laravel_migration_path;

#[WithConfig('database.default', 'testing')]
class MigrateWithLaravelTest extends TestCase
{
    #[Test]
    #[DefineDatabase('loadApplicationMigrations')]
    public function it_loads_the_migrations()
    {
        $now = Carbon::now();

        DB::table('users')->insert([
            'name' => 'Orchestra',
            'email' => 'crynobone@gmail.com',
            'password' => \Hash::make('456'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $users = DB::table('users')->where('id', '=', 1)->first();

        $this->assertEquals('crynobone@gmail.com', $users->email);
        $this->assertTrue(Hash::check('456', $users->password));
    }

    #[Test]
    #[DefineDatabase('runApplicationMigrations')]
    public function it_runs_the_migrations()
    {
        $now = Carbon::now();

        DB::table('users')->insert([
            'name' => 'Orchestra',
            'email' => 'crynobone@gmail.com',
            'password' => Hash::make('456'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $users = DB::table('users')->where('id', '=', 1)->first();

        $this->assertEquals('crynobone@gmail.com', $users->email);
        $this->assertTrue(Hash::check('456', $users->password));
    }

    public function loadApplicationMigrations()
    {
        $this->loadLaravelMigrations(['--database' => 'testing']);
    }

    public function runApplicationMigrations()
    {
        after_resolving($this->app, 'migrator', function ($migrator) {
            $migrator->path(laravel_migration_path());
        });

        $this->runLaravelMigrations(['--database' => 'testing']);
    }
}
