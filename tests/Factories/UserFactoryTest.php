<?php

namespace Orchestra\Testbench\Tests\Factories;

use Carbon\CarbonInterface;
use Illuminate\Foundation\Auth\User;
use Orchestra\Testbench\Factories\UserFactory;
use Orchestra\Testbench\TestCase;

class UserFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_make_user_from_factory()
    {
        $user = UserFactory::new()->make([
            'name' => 'Mior Muhammad Zaki',
            'email' => 'crynobone@gmail.com',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('Mior Muhammad Zaki', $user->name);
        $this->assertSame('crynobone@gmail.com', $user->email);
        $this->assertInstanceOf(CarbonInterface::class, $user->email_verified_at);
    }

    /**
     * @test
     */
    public function it_can_make_unverified_user_from_factory()
    {
        $user = UserFactory::new()->unverified()->make([
            'name' => 'Mior Muhammad Zaki',
            'email' => 'crynobone@gmail.com',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('Mior Muhammad Zaki', $user->name);
        $this->assertSame('crynobone@gmail.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }
}
