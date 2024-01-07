<?php

namespace Orchestra\Testbench\Tests\Factories;

use Carbon\CarbonInterface;
use Illuminate\Foundation\Auth\User;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\Factories\UserFactory;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UserFactoryTest extends TestCase
{
    use WithWorkbench;

    #[Test]
    public function it_has_the_default_configuration()
    {
        $this->assertSame(User::class, config('auth.providers.users.model'));
        $this->assertNull(env('AUTH_MODEL'));
    }

    #[Test]
    public function it_can_generate_user()
    {
        $user = UserFactory::new()->make();

        $this->assertInstanceOf(User::class, $user);
        $this->assertFalse($user->exists);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->email_verified_at);
        $this->assertInstanceOf(CarbonInterface::class, $user->email_verified_at);
    }

    #[Test]
    public function it_can_generate_unverified_user()
    {
        $user = UserFactory::new()->unverified()->make();

        $this->assertInstanceOf(User::class, $user);
        $this->assertFalse($user->exists);
        $this->assertNotNull($user->email);
        $this->assertNull($user->email_verified_at);
    }
}
