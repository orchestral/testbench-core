<?php

namespace Orchestra\Testbench\Tests\Concerns\Database;

use Illuminate\Contracts\Config\Repository;
use Mockery as m;
use Orchestra\Testbench\Concerns\Database\HandlesConnections;
use PHPUnit\Framework\TestCase;

class HandlesConnectionsTest extends TestCase
{
    use HandlesConnections;

    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_build_mysql_connection()
    {
        $config = m::mock(Repository::class);

        $_ENV['MYSQL_URL'] = 'mysql://127.0.0.1:3306';

        $config->shouldReceive('get')->never()->with('database.connections.mysql.url')->andReturnNull()
            ->shouldReceive('get')->once()->with('database.connections.mysql.host')->andReturn('127.0.0.1')
            ->shouldReceive('get')->once()->with('database.connections.mysql.port')->andReturn('3306')
            ->shouldReceive('get')->once()->with('database.connections.mysql.database')->andReturn('laravel')
            ->shouldReceive('get')->once()->with('database.connections.mysql.username')->andReturn('root')
            ->shouldReceive('get')->once()->with('database.connections.mysql.password')->andReturn('secret')
            ->shouldReceive('set')->once()->with([
                'database.connections.mysql.url' => 'mysql://127.0.0.1:3306',
                'database.connections.mysql.host' => '127.0.0.1',
                'database.connections.mysql.port' => '3306',
                'database.connections.mysql.database' => 'laravel',
                'database.connections.mysql.username' => 'root',
                'database.connections.mysql.password' => 'secret',
            ]);

        $this->usesDatabaseConnectionsEnvironmentVariables($config, 'mysql', 'MYSQL');
    }
}
