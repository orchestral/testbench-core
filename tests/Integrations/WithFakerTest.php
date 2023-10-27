<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Faker\Generator;
use Illuminate\Foundation\Testing\WithFaker;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class WithFakerTest extends TestCase
{
    use WithFaker;

    #[Test]
    public function it_can_use_faker()
    {
        $this->assertInstanceOf(Generator::class, $this->faker);
    }
}
