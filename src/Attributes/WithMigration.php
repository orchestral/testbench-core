<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Illuminate\Support\Collection;
use Orchestra\Testbench\Contracts\Attributes\Invokable as InvokableContract;

use function Orchestra\Testbench\laravel_migration_path;
use function Orchestra\Testbench\load_migration_paths;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class WithMigration implements InvokableContract
{
    /**
     * The target types.
     *
     * @var array<int, string>
     */
    public readonly array $types;

    /**
     * Construct a new attribute.
     */
    public function __construct()
    {
        $this->types = Collection::make(\func_num_args() > 0 ? \func_get_args() : ['laravel'])
            ->transform(static fn ($type) => \in_array($type, ['cache', 'session']) ? 'laravel' : $type)
            ->all();
    }

    /**
     * Handle the attribute.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    public function __invoke($app): void
    {
        /** @var array<int, string> $types */
        $types = Collection::make($this->types)
            ->transform(static fn ($type) => laravel_migration_path($type !== 'laravel' ? $type : null))
            ->all();

        load_migration_paths($app, $types);
    }
}
