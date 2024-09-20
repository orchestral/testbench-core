<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Closure;
use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\Contracts\Attributes\Actionable as ActionableContract;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class RequiresDatabase implements ActionableContract
{
    /**
     * Construct a new attribute.
     *
     * @param  string  $driver
     * @param  string|null  $versionRequirement
     * @param  bool  $default
     */
    public function __construct(
        public string $driver,
        public ?string $versionRequirement = null,
        public bool $default = true
    ) {
        //
    }

    /**
     * Handle the attribute.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @param  \Closure(string, array<int, mixed>):void  $action
     * @return void
     */
    public function handle($app, Closure $action): void
    {
        $connection = DB::connection($this->driver);

        if ($this->default === true && (DB::connection() !== $connection)) {
            \call_user_func($action, 'markTestSkipped', [\sprintf('Requires %s as the default database connection', $connection->getName())]);
        }

        if (
            preg_match('/(?P<operator>[<>=!]{0,2})\s*(?P<version>[\d\.-]+(dev|(RC|alpha|beta)[\d\.])?)[ \t]*\r?$/m', $this->versionRequirement, $matches)
        ) {
            if (empty($matches['operator'])) {
                $matches['operator'] = '>=';
            }

            if (! version_compare($connection->getServerVersion(), $matches['version'], $matches['operator'])) {
                \call_user_func($action, 'markTestSkipped', [\sprintf('Requires %s:%s', $connection->getName(), $this->versionRequirement)]);
            }
        }
    }
}
