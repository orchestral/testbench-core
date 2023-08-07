<?php

namespace Orchestra\Testbench\Workbench\Http\Middleware;

use Closure;
use function Orchestra\Testbench\workbench;

class CatchDefaultRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $workbench = workbench();

        if ($request->is('/') && ! \is_null($workbench['user'])) {
            return redirect('/_workbench');
        }

        return $next($request);
    }
}
