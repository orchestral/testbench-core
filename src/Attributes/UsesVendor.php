<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Orchestra\Testbench\Contracts\Attributes\AfterEach as AfterEachContract;
use Orchestra\Testbench\Contracts\Attributes\BeforeEach as BeforeEachContract;
use Orchestra\Testbench\Foundation\Application;

use function Orchestra\Testbench\package_path;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class UsesVendor implements AfterEachContract, BeforeEachContract
{
    public ?bool $vendorSymlinkCreated = null;

    /**
     * Handle the attribute.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function beforeEach($app): void
    {
        $vendorPath = $app->basePath('vendor');

        if (is_link($vendorPath)) {
            $this->vendorSymlinkCreated = false;

            return;
        }

        Application::createVendorSymlink(base_path(), package_path('vendor'));
        $this->vendorSymlinkCreated = true;
    }

    /**
     * Handle the attribute.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function afterEach($app): void
    {
        $vendorPath = $app->basePath('vendor');

        if (is_link($vendorPath) && $this->vendorSymlinkCreated === true) {
            $app['files']->delete($vendorPath);
        }
    }
}
