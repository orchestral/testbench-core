<?php

namespace Orchestra\Testbench\Foundation\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\Foundation\Config;

class WorkbenchController
{
    /**
     * Start page.
     *
     * @param  \Orchestra\Testbench\Contracts\Config|null  $config
     * @return \Illuminate\Http\RedirectResponse
     */
    public function start(?ConfigContract $config = null)
    {
        $workbench = with($config ?? new Config(), function ($config) {
            return $config->getWorkbenchAttributes();
        });

        if (\is_null($workbench['user'])) {
            $this->logout($workbench['guard']);
        } else {
            $this->login($workbench['user'], $workbench['guard']);
        }

        /** @phpstan-ignore-next-line */
        return redirect($workbench['start']);
    }

    /**
     * Retrieve the authenticated user identifier and class name.
     *
     * @param  string|null  $guard
     * @return array
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
     * @return void
     */
    public function login($userId, $guard = null)
    {
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
    }

    /**
     * Log the user out of the application.
     *
     * @param  string|null  $guard
     * @return void
     */
    public function logout($guard = null)
    {
        $guard = $guard ?: config('auth.defaults.guard');

        /** @phpstan-ignore-next-line */
        Auth::guard($guard)->logout();

        Session::forget('password_hash_'.$guard);
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
}
