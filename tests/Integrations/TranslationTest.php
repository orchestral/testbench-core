<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TranslationTest extends TestCase
{
    #[Test]
    public function it_can_resolve_default_language_path()
    {
        $this->assertSame(base_path('lang'), $this->app->langPath());
    }

    #[Test]
    public function it_can_resolve_validation_language_string()
    {
        $this->assertSame('The name field is required.', __('validation.required', ['attribute' => 'name']));
    }
}
