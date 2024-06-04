<?php

namespace Orchestra\Testbench\Tests\Workbench;

use Orchestra\Testbench\Attributes\RequiresLaravel;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

#[WithConfig('app.key', 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF')]
class ErrorPageTest extends TestCase
{
    use WithWorkbench;

    #[Test]
    #[WithConfig('app.debug', true)]
    public function it_can_resolve_exception_page()
    {
        $this->get('/failed')
            ->assertInternalServerError()
            ->assertSee('RuntimeException')
            ->assertSee('Bad route!');
    }

    #[Test]
    #[WithConfig('app.debug', true)]
    public function it_can_resolve_exception_without_exception_handling()
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Bad route!');

        $this->withoutExceptionHandling()
            ->get('/failed');
    }

    #[Test]
    public function it_can_resolve_exception_page_without_enabling_debug_mode()
    {
        $this->get('/failed')
            ->assertInternalServerError()
            ->assertSee('500')
            ->assertSee('Server Error');
    }

    #[Test]
    public function it_can_resolve_exception_without_exception_handling_without_enabling_debug_mode()
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Bad route!');

        $this->withoutExceptionHandling()
            ->get('/failed');
    }

    #[Test]
    #[WithConfig('app.debug', true)]
    public function it_can_resolve_exception_page_using_json_request()
    {
        $this->getJson('/api/failed')
            ->assertInternalServerError()
            ->assertSee('RuntimeException')
            ->assertSee('Bad route!');
    }

    #[Test]
    #[WithConfig('app.debug', true)]
    public function it_can_resolve_exception_using_json_request_without_exception_handling()
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Bad route!');

        $this->withoutExceptionHandling()
            ->get('/failed');
    }
}
