<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Foundation\Testing\WithFaker;
use Orchestra\Testbench\TestCase;

class FakerLocaleTest extends TestCase
{
    use WithFaker;

    /**
     * Get default faker locale.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return string|null
     */
    protected function getFakerLocale($app)
    {
        return 'it_IT';
    }

    /** @test */
    public function it_can_override_faker_locale()
    {
        $providerNames = array_map(function ($p) {
            return get_class($p);
        }, $this->faker()->getProviders());

        $this->assertContains('Faker\Provider\it_IT\Person', $providerNames);
    }
}
