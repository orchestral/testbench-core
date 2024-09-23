<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Orchestra\Testbench\Contracts\Attributes\Actionable as ActionableContract;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class RequiresDatabase implements ActionableContract
{
    /**
     * Construct a new attribute.
     *
     * @param  string  $driver
     * @param  string|null  $versionRequirement
     * @param  string|null  $connection
     * @param  bool|null  $default
     */
    public function __construct(
        public array|string $driver,
        public ?string $versionRequirement = null,
        public ?string $connection = null,
        public ?bool $default = null
    ) {
        if (\is_null($connection) && \is_string($driver)) {
            $this->default = true;
        }

        if (\is_array($driver) && $default === true) {
            throw new InvalidArgumentException('Unable to validate default connection when given an array of database drivers');
        }
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
        $connection = DB::connection($this->connection);

        if (
            ($this->default ?? false) === true
            && \is_string($this->driver)
            && $connection->getDriverName() !== $this->driver
        ) {
            \call_user_func($action, 'markTestSkipped', [\sprintf('Requires %s as the default database connection', $connection->getName())]);
        }

        $drivers = Arr::wrap($this->driver);
        $usingCorrectConnection = false;

        foreach ($drivers as $driver) {
            if ($connection->getDriverName() === $driver) {
                $usingCorrectConnection = true;
            }
        }

        if ($usingCorrectConnection === false) {
            \call_user_func(
                $action,
                'markTestSkipped',
                [\sprintf('Requires %s to use [%s] database connection', $connection->getName(), Arr::join($drivers, ','))]
            );
        }

        if (
            ! \is_null($this->versionRequirement)
            && preg_match('/(?P<operator>[<>=!]{0,2})\s*(?P<version>[\d\.-]+(dev|(RC|alpha|beta)[\d\.])?)[ \t]*\r?$/m', $this->versionRequirement, $matches)
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
