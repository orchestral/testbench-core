<?php

namespace Orchestra\Testbench\Tests;

use Faker\Generator;
use Illuminate\Foundation\Testing\WithFaker;
use Orchestra\Testbench\TestCase;

class WithFakerTest extends TestCase
{
    use WithFaker;

    /** @test */
    public function it_can_use_faker()
    {
        $this->assertInstanceOf(Generator::class, $this->faker);
    }
}
