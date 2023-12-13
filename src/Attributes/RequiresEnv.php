<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Closure;
use Orchestra\Testbench\Contracts\Attributes\Actionable as ActionableContract;
use Orchestra\Testbench\Foundation\Env;
use Orchestra\Testbench\Foundation\UndefinedValue;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class RequiresEnv implements ActionableContract
{
    /**
     * The target environment key.
     *
     * @var string
     */
    public $key;

    /**
     * The target missing key message.
     *
     * @var string|null
     */
    public $message;

    /**
     * Construct a new attribute.
     *
     * @param  string  $key
     * @param  string|null  $message
     */
    public function __construct(string $key, ?string $message = null)
    {
        $this->key = $key;
        $this->message = $message;
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
        $value = Env::get($this->key, new UndefinedValue());

        $message = $this->message ?? "Missing required environment variable `{$this->key}`";

        if ($value instanceof UndefinedValue) {
            \call_user_func($action, 'markTestSkipped', [$message]);
        }
    }
}
