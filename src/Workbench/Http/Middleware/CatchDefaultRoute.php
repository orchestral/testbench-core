<?php

namespace Orchestra\Testbench\Workbench\Http\Middleware;

use Closure;
use function Orchestra\Testbench\workbench;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        if ($request->decodedPath() === '/' && ! \is_null($workbench['user']) && \is_null($request->user())) {
            return redirect('/_workbench');
        }

        $response = $next($request);

        if (! \is_null($response->exception) && $response->exception instanceof NotFoundHttpException) {
            if ($request->decodedPath() === '/' && $workbench['start'] !== '/') {
                return redirect($workbench['start']);
            }
        }

        return $response;
    }
}
