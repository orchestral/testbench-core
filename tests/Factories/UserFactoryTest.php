<?php

namespace Orchestra\Testbench\Tests\Factories;

use Carbon\CarbonInterface;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\Factories\UserFactory;
use Orchestra\Testbench\TestCase;

class UserFactoryTest extends TestCase
{
    use WithWorkbench;

    /** @test */
    public function it_can_generate_user()
    {
        $user = UserFactory::new()->make();

        $this->assertFalse($user->exists);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->email_verified_at);
        $this->assertInstanceOf(CarbonInterface::class, $user->email_verified_at);
    }

    /** @test */
    public function it_can_generate_unverified_user()
    {
        $user = UserFactory::new()->unverified()->make();

        $this->assertFalse($user->exists);
        $this->assertNotNull($user->email);
        $this->assertNull($user->email_verified_at);
    }
}
