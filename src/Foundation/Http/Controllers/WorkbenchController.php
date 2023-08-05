<?php

namespace Orchestra\Testbench\Foundation\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\Foundation\Config;

/**
 * @phpstan-import-type TWorkbenchConfig from \Orchestra\Testbench\Foundation\Config
 */
class WorkbenchController extends Controller
{
    /**
     * Workbench configuration.
     *
     * @var array<string, mixed>|null
     *
     * @phpstan-var TWorkbenchConfig|null
     */
    protected $cachedWorkbenchConfig;

    /**
     * Start page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function start()
    {
        $workbench = $this->workbenchConfig();

        if (\is_null($workbench['user'])) {
            return $this->logout($workbench['guard']);
        }

        return $this->login((string) $workbench['user'], $workbench['guard']);
    }

    /**
     * Retrieve the authenticated user identifier and class name.
     *
     * @param  string|null  $guard
     * @return array<string, mixed>
     *
     * @phpstan-return array{id?: string|int, className?: string}
     */
    public function user($guard = null)
    {
        $user = Auth::guard($guard)->user();

        if (! $user) {
            return [];
        }

        return [
            'id' => $user->getAuthIdentifier(),
            'className' => \get_class($user),
        ];
    }

    /**
     * Login using the given user ID / email.
     *
     * @param  string  $userId
     * @param  string|null  $guard
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login($userId, $guard = null)
    {
        $workbench = $this->workbenchConfig();
        $guard = $guard ?: config('auth.defaults.guard');

        /**
         * @phpstan-ignore-next-line
         *
         * @var \Illuminate\Contracts\Auth\UserProvider $provider
         */
        $provider = Auth::guard($guard)->getProvider();

        $user = Str::contains($userId, '@')
            ? $provider->retrieveByCredentials(['email' => $userId])
            : $provider->retrieveById($userId);

        /** @phpstan-ignore-next-line */
        Auth::guard($guard)->login($user);

        /** @phpstan-ignore-next-line */
        return redirect($workbench['start']);
    }

    /**
     * Log the user out of the application.
     *
     * @param  string|null  $guard
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout($guard = null)
    {
        $workbench = $this->workbenchConfig();

        $guard = $guard ?: config('auth.defaults.guard');

        /** @phpstan-ignore-next-line */
        Auth::guard($guard)->logout();

        Session::forget('password_hash_'.$guard);

        /** @phpstan-ignore-next-line */
        return redirect($workbench['start']);
    }

    /**
     * Get the model for the given guard.
     *
     * @param  string  $guard
     * @return string
     */
    protected function modelForGuard($guard)
    {
        $provider = config("auth.guards.{$guard}.provider");

        return config("auth.providers.{$provider}.model");
    }

    /**
     * Get or resolve workbench configuration.
     *
     * @return array<string, mixed>
     *
     * @phpstan-return TWorkbenchConfig
     */
    protected function workbenchConfig(): array
    {
        if (! isset($this->cachedWorkbenchConfig)) {
            $config = app()->bound(ConfigContract::class)
                ? app(ConfigContract::class)
                : new Config();

            $this->cachedWorkbenchConfig = $config->getWorkbenchAttributes();
        }

        return $this->cachedWorkbenchConfig;
    }
}
