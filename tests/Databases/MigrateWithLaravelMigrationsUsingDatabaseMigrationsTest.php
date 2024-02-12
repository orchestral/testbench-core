<?php

namespace Orchestra\Testbench\Tests\Databases;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Concerns\WithLaravelMigrations;
use Orchestra\Testbench\TestCase;

#[WithConfig('database.default', 'testing')]
class MigrateWithLaravelMigrationsUsingDatabaseMigrationsTest extends TestCase
{
    use DatabaseMigrations;
    use WithLaravelMigrations;

    /** @test */
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
}
