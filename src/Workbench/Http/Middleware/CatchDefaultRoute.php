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

        if ($request->decodedPath() === '/' && ! \is_null($workbench['user'])) {
            return redirect(
                is_null($request->user()) ? '/_workbench' : $workbench['start']
            );
        }

        $response = $next($request);

        if (! is_null($response->exception) && $response->exception instanceof NotFoundHttpException) {
            if ($request->decodedPath() === '/' && $workbench['start'] !== '/') {
                return redirect($workbench['start']);
            }
        }

        return $response;
    }
}
