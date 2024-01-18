<?php

namespace Orchestra\Testbench\Tests\Foundation\Concerns;

use Illuminate\Contracts\Config\Repository;
use Mockery as m;
use Orchestra\Testbench\Foundation\Concerns\HandlesDatabaseConnections;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HandlesDatabaseConnectionsTest extends TestCase
{
    use HandlesDatabaseConnections;

    /** {@inheritDoc} */
    #[\Override]
    protected function tearDown(): void
    {
        m::close();
    }

    #[Test]
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
            ->shouldReceive('get')->once()->with('database.connections.mysql.collation')->andReturn('utf8mb4_0900_ai_ci')
            ->shouldReceive('set')->once()->with([
                'database.connections.mysql.url' => 'mysql://127.0.0.1:3306',
                'database.connections.mysql.host' => '127.0.0.1',
                'database.connections.mysql.port' => '3306',
                'database.connections.mysql.database' => 'laravel',
                'database.connections.mysql.username' => 'root',
                'database.connections.mysql.password' => 'secret',
                'database.connections.mysql.collation' => 'utf8mb4_0900_ai_ci',
            ]);

        $this->usesDatabaseConnectionsEnvironmentVariables($config, 'mysql', 'MYSQL');
    }
}
