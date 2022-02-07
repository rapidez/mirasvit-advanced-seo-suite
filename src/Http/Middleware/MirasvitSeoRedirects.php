<?php

namespace Rapidez\MirasvitAdvancedSeoSuite\Http\Middleware;

use Closure;
use Rapidez\MirasvitAdvancedSeoSuite\Models\Redirect;

class MirasvitSeoRedirects
{
    public function handle($request, Closure $next)
    {
        if ($redirect = Redirect::query()
            ->where('url_from', $request->path())
            ->orWhere('url_from', '/'.$request->path())
            ->first()) {
            return redirect($redirect->url_to, $redirect->redirect_type);
        }

        return $next($request);
    }
}
