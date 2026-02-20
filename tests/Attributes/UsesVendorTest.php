<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Illuminate\Contracts\Config\Repository as ConfigRepositoryContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Orchestra\Testbench\Attributes\UsesVendor;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Tests\TestCase;

use function Orchestra\Sidekick\Filesystem\join_paths;
use function Orchestra\Testbench\package_path;

class UsesVendorTest extends TestCase
{
    use LazilyRefreshDatabase;

    /** @test */
    #[UsesVendor]
    public function it_can_uses_vendor_attribute()
    {
        $filesystem = new Filesystem;

        $this->assertSame(
            $filesystem->hash(base_path(join_paths('vendor', 'autoload.php'))),
            $filesystem->hash(package_path('vendor', 'autoload.php'))
        );
    }

    /** @test */
    #[UsesVendor]
    public function it_can_uses_config_from_attribute()
    {
        tap($this->app->make('config'), function ($repository) {
            $this->assertInstanceOf(ConfigRepositoryContract::class, $repository);
        });
    }

    /** @test */
    #[UsesVendor]
    #[WithMigration]
    #[WithConfig('database.default', 'testing')]
    public function it_can_resolve_config_from_container()
    {
        $user = User::query()->count();

        $this->assertSame(0, $user);
    }
}
