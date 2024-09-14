<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Faker\Generator;
use Illuminate\Foundation\Testing\WithFaker;
use Orchestra\Testbench\Attributes\WithConfig;
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

    /** @test */
    #[WithConfig('app.faker_locale', 'it_IT')]
    public function it_can_override_faker_locale()
    {
        $providerNames = array_map(function ($p) {
            return \get_class($p);
        }, $this->faker()->getProviders());

        $this->assertContains('Faker\Provider\it_IT\Person', $providerNames);
    }
}
